<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace DaftFramework\RelaxedObjectRepository\Fixtures;

use DaftFramework\RelaxedObjectRepository\AppendableObjectRepository;
use DaftFramework\RelaxedObjectRepository\ObjectEasyDBRepository as Base;
use DaftFramework\RelaxedObjectRepository\PatchableObjectRepository;
use ParagonIE\EasyDB\EasyDB;

/**
 * @template T1 as object
 * @template T2 as array<string, scalar>
 * @template T3 as array<string, scalar|null>
 * @template T4 as array{table:string, ParagonIE\EasyDB\EasyDB:EasyDB}
 *
 * @template-extends Base<T1, T2, T3, T4>
 *
 * @template-implements AppendableObjectRepository<T1, T2, T3, T4>
 * @template-implements PatchableObjectRepository<T1, T2, T3, T4>
 */
abstract class ObjectEasyDBRepository extends Base implements
		AppendableObjectRepository,
		PatchableObjectRepository
{
}
