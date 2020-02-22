<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace DaftFramework\RelaxedObjectRepository;

use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Factory;

/**
 * @template S as array{id:int, name:string}
 * @template S2 as array{id:int|string, name:string}
 * @template S3 as array{name:string}
 * @template T1 as Fixtures\Thing
 * @template T2 as array{type:class-string<Fixtures\Thing>, table:string, ParagonIE\EasyDB\EasyDB:EasyDB}
 * @template T3 as Fixtures\ThingEasyDBRepository
 * @template T4 as Fixtures\ThingEasyDBRepository
 *
 * @template-extends ObjectEasyDBRepositoryTest<S, S2, S3, T1, T2, T3, T4>
 */
class ThingEasyDBRepositoryTest extends ObjectEasyDBRepositoryTest
{
	/**
	 * @return list<
	 *	array{
	 *		0:class-string<T3>,
	 *		1:T2,
	 *		2:list<S>,
	 *		3:list<S2>
	 *	}
	 * >
	 */
	public function dataProviderAppendObject() : array
	{
		$out = [
			[
				Fixtures\ThingEasyDBRepository::class,
				[
					'table' => 'things',
					EasyDB::class => Factory::create('sqlite::memory:'),
				],
				[
					[
						'id' => 0,
						'name' => 'foo',
					],
				],
				[
					[
						'id' => '1',
						'name' => 'foo',
					],
				],
			],
		];

		if ('true' === getenv('TRAVIS')) {
			$out[] = [
				Fixtures\ThingEasyDBRepository::class,
				[
					'table' => 'things',
					EasyDB::class => Factory::create(
						'mysql:host=localhost;dbname=travis',
						'travis',
						''
					),
				],
				[
					[
						'id' => 0,
						'name' => 'foo',
					],
				],
				[
					[
						'id' => '1',
						'name' => 'foo',
					],
				],
			];
		}

		/**
		 * @var list<
		 *	array{
		 *		0:class-string<T3>,
		 *		1:T2,
		 *		2:list<S>,
		 *		3:list<S2>
		 *	}
		 * >
		 */
		return $out;
	}

	/**
	 * @return list<
	 *	array{
	 *		0:class-string<T4>,
	 *		1:T2,
	 *		2:S,
	 *		3:S3,
	 *		4:S2
	 *	}
	 * >
	 */
	public function dataProviderPatchObject() : array
	{
		$out = [
			[
				Fixtures\ThingEasyDBRepository::class,
				[
					'table' => 'things',
					EasyDB::class => Factory::create('sqlite::memory:'),
				],
				[
					'id' => 0,
					'name' => 'foo',
				],
				[
					'name' => 'bar',
				],
				[
					'id' => 1,
					'name' => 'bar',
				],
			],
		];

		if ('true' === getenv('TRAVIS')) {
			$out[] = [
				Fixtures\ThingEasyDBRepository::class,
				[
					'table' => 'things',
					EasyDB::class => Factory::create(
						'mysql:host=localhost;dbname=travis',
						'travis',
						''
					),
				],
				[
					'id' => 0,
					'name' => 'foo',
				],
				[
					'name' => 'bar',
				],
				[
					'id' => 1,
					'name' => 'bar',
				],
			];
		}

		/**
		 * @var list<
		 *	array{
		 *		0:class-string<T4>,
		 *		1:T2,
		 *		2:S,
		 *		3:S3,
		 *		4:S2
		 *	}
		 * >
		 */
		return $out;
	}

	/**
	 * @dataProvider dataProviderAppendObject
	 *
	 * @covers \DaftFramework\RelaxedObjectRepository\Fixtures\ThingEasyDBRepository::__construct()
	 * @covers \DaftFramework\RelaxedObjectRepository\Fixtures\ThingEasyDBRepository::MaybeRecallObject()
	 * @covers \DaftFramework\RelaxedObjectRepository\Fixtures\ThingEasyDBRepository::RemoveObject()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::__construct()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::PurgeObjectDataCache()
	 *
	 * @param class-string<T3> $repo_type
	 * @param T2 $repo_args
	 * @param list<S> $append_these
	 * @param list<S2> $expect_these
	 */
	public function test_append_object(
		string $repo_type,
		array $repo_args,
		array $append_these,
		array $expect_these
	) : void {
		parent::test_append_object(
			$repo_type,
			$repo_args,
			$append_these,
			$expect_these
		);
	}

	/**
	 * @dataProvider dataProviderAppendObject
	 *
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::__construct()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::MaybeRecallObject()
	 *
	 * @depends test_append_object
	 *
	 * @param class-string<T3> $repo_type
	 * @param T2 $repo_args
	 * @param list<S> $append_these
	 * @param list<S2> $expect_these
	 */
	public function test_default_failure(
		string $repo_type,
		array $repo_args,
		array $append_these,
		array $expect_these
	) : void {
		parent::test_default_failure(
			$repo_type,
			$repo_args,
			$append_these,
			$expect_these
		);
	}

	/**
	 * @dataProvider dataProviderAppendObject
	 *
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::__construct()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::MaybeRecallObject()
	 *
	 * @depends test_append_object
	 *
	 * @param class-string<T3> $repo_type
	 * @param T2 $repo_args
	 * @param list<S> $append_these
	 * @param list<S2> $expect_these
	 */
	public function test_custom_failure(
		string $repo_type,
		array $repo_args,
		array $append_these,
		array $expect_these
	) : void {
		parent::test_custom_failure(
			$repo_type,
			$repo_args,
			$append_these,
			$expect_these
		);
	}

	/**
	 * @dataProvider dataProviderPatchObject
	 *
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::__construct()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::MaybeRecallObject()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::PatchObjectData()
	 *
	 * @depends test_append_object
	 *
	 * @param class-string<T4> $repo_type
	 * @param T2 $repo_args
	 * @param S $append_this
	 * @param S3 $patch_this
	 * @param S2 $expect_this
	 */
	public function test_patch_object(
		string $repo_type,
		array $repo_args,
		array $append_this,
		array $patch_this,
		array $expect_this
	) : void {
		parent::test_patch_object(
			$repo_type,
			$repo_args,
			$append_this,
			$patch_this,
			$expect_this
		);
	}

	/**
	 * @return list<array{0:class-string<T3>, 1:T2, 2:S, 3:S2}>
	 */
	public function PersistingDataRecallProvider() : array
	{
		$out = [
			[
				Fixtures\ThingEasyDBRepository::class,
				[
					'table' => 'things',
					EasyDB::class => Factory::create('sqlite::memory:'),
				],
				[
					'id' => 0,
					'name' => 'foo',
				],
				[
					'id' => 1,
					'name' => 'foo',
				],
			],
		];

		if ('true' === getenv('TRAVIS')) {
			$out[] = [
				Fixtures\ThingEasyDBRepository::class,
				[
					'table' => 'things',
					EasyDB::class => Factory::create(
						'mysql:host=localhost;dbname=travis',
						'travis',
						''
					),
				],
				[
					'id' => 0,
					'name' => 'foo',
				],
				[
					'id' => 1,
					'name' => 'foo',
				],
			];
		}

		/**
		 * @var list<array{0:class-string<T3>, 1:T2, 2:S, 3:S2}>
		 */
		return $out;
	}

	/**
	 * @dataProvider PersistingDataRecallProvider
	 *
	 * @covers \DaftFramework\RelaxedObjectRepository\Fixtures\ThingEasyDBRepository::__construct()
	 * @covers \DaftFramework\RelaxedObjectRepository\Fixtures\ThingEasyDBRepository::AppendObjectFromArray()
	 * @covers \DaftFramework\RelaxedObjectRepository\Fixtures\ThingEasyDBRepository::ObtainIdFromObject()
	 * @covers \DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository::__construct()
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
		parent::test_persisting_data_recall(
			$repo_type,
			$repo_args,
			$append_this,
			$expect_this
		);
	}
}
