<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace DaftFramework\RelaxedObjectRepository;

use ParagonIE\EasyDB\EasyDB;
use PDO;

/**
 * @template T1 as object
 * @template T2 as array<string, scalar>
 * @template T3 as array<string, scalar|null>
 * @template T4 as array{table:string, ParagonIE\EasyDB\EasyDB:EasyDB}
 *
 * @template-extends AbstractObjectRepository<T1, T2, T4>
 *
 * @template-implements ConvertingRepository<T1, T3, T2, T4>
 */
abstract class ObjectEasyDBRepository extends AbstractObjectRepository implements ConvertingRepository
{
	protected EasyDB $connection;

	protected string $table;

	/** @var array<string, T3> */
	protected array $data = [];

	/** @var array<string, T1> */
	protected array $memory = [];

	/**
	 * @param T4 $options
	 */
	public function __construct(array $options)
	{
		$this->connection = $options[EasyDB::class];
		$this->table = $options['table'];
	}

	/**
	 * @param T2 $id
	 */
	public function RemoveObject(array $id) : void
	{
		$this->connection->delete($this->table, $id);

		$this->ForgetObject($id);

		$this->PurgeObjectDataCache($id);
	}

	/**
	 * @param T2 $id
	 */
	public function PurgeObjectDataCache(array $id) : void
	{
		$hash = static::RelaxedObjectHash($id);

		unset($this->data[$hash]);
	}

	/**
	 * @param T2 $id
	 */
	public function MaybeRecallObject(array $id) : ? object
	{
		$cached = parent::MaybeRecallObject($id);

		$hash = static::RelaxedObjectHash($id);

		if (null === $cached && isset($this->data[$hash])) {
			$cached = $this->ConvertSimpleArrayToObject(
				$this->data[$hash]
			);

			$this->memory[$hash] = $cached;
		} elseif (null === $cached) {
			/** @var list<string> */
			$keys = array_keys($id);

			$sth = $this->connection->prepare(
				'SELECT * FROM ' .
				$this->connection->escapeIdentifier($this->table) .
				' WHERE ' .
				implode(' AND ', array_map(
					function (string $field) : string {
						return
							$this->connection->escapeIdentifier($field) .
							' = ?';
					},
					$keys
				)) .
				' LIMIT 1'
			);

			$sth->execute(array_values($id));

			/** @var T3|false|null */
			$row = $sth->fetch(PDO::FETCH_ASSOC);

			if (null !== $row && false !== $row) {
				$this->memory[
					$hash
				] = $cached = $this->ConvertSimpleArrayToObject($row);

				$this->data[$hash] = $this->ConvertObjectToSimpleArray(
					$this->memory[$hash]
				);
			}
		}

		return $cached;
	}

	/**
	 * @param T2 $id
	 * @param T3 $data
	 */
	public function PatchObjectData(array $id, array $data) : void
	{
		$this->ForgetObject($id);
		$this->connection->update($this->table, $data, $id);

		$hash = static::RelaxedObjectHash($id);

		if (isset($this->data[$hash])) {
			foreach ($data as $k => $v) {
				$this->data[$hash][$k] = $v;
			}
		}
	}

	public function UpdateObject(object $object) : void
	{
		$id = $this->ObtainIdFromObject($object);
		$id_keys = array_keys($id);
		/** @var T3 */
		$data = array_filter(
			$this->ConvertObjectToSimpleArray($object),
			static function (string $key) use ($id_keys) : bool {
				return ! in_array($key, $id_keys, true);
			},
			ARRAY_FILTER_USE_KEY
		);

		$this->PatchObjectData($id, $data);

		parent::UpdateObject($object);
	}
}
