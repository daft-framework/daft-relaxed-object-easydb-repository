<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace DaftFramework\RelaxedObjectRepository\Fixtures;

use DaftFramework\RelaxedObjectRepository\AppendableObjectRepository;
use DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository as Base;
use ParagonIE\EasyDB\EasyDB;

/**
 * @template T1 as Thing
 * @template T2 as array{id:int}
 * @template T3 as array{id:int, name:string}
 * @template T4 as array{table:string, ParagonIE\EasyDB\EasyDB:EasyDB}
 *
 * @template-extends ObjectEasyDBRepository<T1, T2, T3, T4>
 */
class ThingEasyDBRepository extends ObjectEasyDBRepository
{
	/** @var array<string, Thing> */
	protected array $memory = [];

	/**
	 * @param T4 $options
	 */
	public function __construct(
		array $options
	) {
		parent::__construct(
			$options
		);

		$connection = $this->connection;
		$table = $this->table;

		$query =
			'CREATE TABLE IF NOT EXISTS ' .
			$connection->escapeIdentifier($table) .
			' ( ' .
			$connection->escapeIdentifier('id') .
			(
				'sqlite' === $connection->getDriver()
					? ' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, '
					: ' INTEGER NOT NULL AUTO_INCREMENT, '
			) .
			$connection->escapeIdentifier('name') .
			' VARCHAR(255) NOT NULL' .
			(
				'sqlite' === $connection->getDriver()
					? ''
					: (
						', PRIMARY KEY(' .
						$connection->escapeIdentifier('id') .
						')'
					)
			) .
			');';

		$connection->query($query);

		if ('sqlite' !== $connection->getDriver()) {
			$connection->query(
				'TRUNCATE TABLE ' .
				$connection->escapeIdentifier($table)
			);
		}
	}

	/**
	 * @param T1 $object
	 *
	 * @return T1
	 */
	public function AppendObject(object $object) : object
	{
		$this->connection->insert($this->table, [
			'name' => $object->name,
		]);

		/** @var T2 */
		$id = ['id' => (int) $this->connection->lastInsertId()];

		$hash = static::RelaxedObjectHash($id);

		/** @var Thing */
		$object = $object;

		$object->id = (int) $id['id'];

		$this->memory[$hash] = $object;
		$this->data[$hash] = $this->ConvertObjectToSimpleArray($object);

		return $this->RecallObject($id);
	}

	/**
	 * @param T3 $data
	 */
	public function AppendObjectFromArray(array $data) : object
	{
		$object = $this->ConvertSimpleArrayToObject($data);

		/** @var T1 */
		return $this->AppendObject($object);
	}

	/**
	 * @param T3 $array
	 */
	public function ConvertSimpleArrayToObject(array $array) : object
	{
		/** @var T1 */
		return new Thing($array['id'], $array['name']);
	}

	/**
	 * @param T1 $object
	 *
	 * @return T3
	 */
	public function ConvertObjectToSimpleArray(object $object) : array
	{
		/** @var T3 */
		return [
			'id' => $object->id,
			'name' => $object->name,
		];
	}

	/**
	 * @param T1 $object
	 */
	public function ObtainIdFromObject(object $object) : array
	{
		return [
			'id' => $object->id,
		];
	}
}
