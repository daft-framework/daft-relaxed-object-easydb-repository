<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace DaftFramework\RelaxedObjectRepository;

use ParagonIE\EasyDB\EasyDB;

/**
 * @template S as array<string, scalar|null>
 * @template S2 as array<string, scalar|null>
 * @template S3 as array<string, scalar|null>
 * @template T1 as object
 * @template T2 as array{type:class-string, table:string, ParagonIE\EasyDB\EasyDB:EasyDB}
 * @template T3 as ObjectEasyDBRepository&AppendableObjectRepository
 * @template T4 as ObjectEasyDBRepository&AppendableObjectRepository&PatchableObjectRepository
 *
 * @template-extends ObjectRepositoryTest<S, S2, S3, T1, T2, T3, T4>
 */
abstract class ObjectEasyDBRepositoryTest extends ObjectRepositoryTest
{
	/**
	 * @return list<array{0:class-string<T3>, 1:T2, 2:S, 3:S2}>
	 */
	abstract public function PersistingDataRecallProvider() : array;

	/**
	 * @dataProvider PersistingDataRecallProvider
	 *
	 * @covers \DaftFramework\RelaxedObjectRepository\AppendableObjectRepository::AppendObjectFromArray()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::__construct()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::ForgetObject()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::MaybeRecallObject()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::PurgeObjectDataCache()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::RemoveObject()
	 *
	 * @param class-string<T3> $repo_type
	 * @param T2 $repo_args
	 * @param S $append_this
	 * @param S2 $expect_this
	 */
	public function test_persisting_data_recall(
		string $repo_type,
		array $repo_args,
		array $append_this,
		array $expect_this
	) : void {
		/** @var T3&AppendableObjectRepository */
		$repo = new $repo_type($repo_args);

		/** @var T1 */
		$object = $repo->AppendObjectFromArray($append_this);

		static::assertSame(
			$expect_this,
			$repo->ConvertObjectToSimpleArray($object)
		);

		/** @var array<string, scalar> */
		$id = $repo->ObtainIdFromObject($object);

		/** @var T1|null */
		$recalled = $repo->MaybeRecallObject($id);

		static::assertSame($object, $recalled);

		$repo->ForgetObject($id);

		/** @var T1|null */
		$recollected = $repo->MaybeRecallObject($id);

		static::assertNotNull($recollected);
		static::assertNotSame($object, $recollected);
		static::assertSame(
			$expect_this,
			$repo->ConvertObjectToSimpleArray($recollected)
		);

		$repo->ForgetObject($id);
		$repo->PurgeObjectDataCache($id);

		/** @var T1|null */
		$recollected = $repo->MaybeRecallObject($id);

		static::assertNotNull($recollected);
		static::assertNotSame($object, $recollected);
		static::assertSame(
			$expect_this,
			$repo->ConvertObjectToSimpleArray($recollected)
		);

		$repo->RemoveObject($id);

		static::assertNull($repo->MaybeRecallObject($id));
	}
}
