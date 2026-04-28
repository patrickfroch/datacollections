<?php declare(strict_types=1);

/**
 * @author      pfroch <info@easySolutionsIT.de>
 * @link        http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT <#YEAR#>
 * @version     2.0.0
 * @since       28.08.2024 - 12:20
 */

namespace Esit\Datacollections;

use PHPUnit\Framework\TestCase;

/**
 * Class EsitTestCase
 */
class EsitTestCase extends TestCase
{


    /**
     * setup the environment
     */
    protected function setUp(): void
    {
        parent::setUp();
    }


    /**
     * tear down the environment
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }


    /**
     * Ersetzt withConsecutive()
     * @param array ...$args
     * @return array
     * @see https://gist.github.com/ziadoz/370fe63e24f31fd1eb989e7477b9a472
     *
     * @example
     * $mock = $this->getMockBuilder(SomeClass::class)->getMock();
     *
     * $mock->expects($this->exactly(3))
     *      ->method('add')
     *      ->with(... $this->consecutiveParams(
     *          ['meta'],
     *          ['title'],
     *          ['caption'],
     *          ['alt']
     *      ))
     *      ->willReturnOnConsecutiveCalls(
     *          $meta, '', '', ''
     *      );
     */
    public function consecutiveParams(array ...$args): array
    {
        $callbacks = [];
        $count = count(max($args));

        for ($index = 0; $index < $count; $index++) {
            $returns = [];

            foreach ($args as $arg) {
                if (! array_is_list($arg)) {
                    throw new \InvalidArgumentException('Every array must be a list');
                }

                if (! isset($arg[$index])) {
                    throw new \InvalidArgumentException(sprintf('Every array must contain %d parameters', $count));
                }

                $returns[] = $arg[$index];
            }

            $callbacks[] = $this->callback(new class ($returns) {
                public function __construct(protected array $returns)
                {
                }

                public function __invoke(mixed $actual): bool
                {
                    if (count($this->returns) === 0) {
                        return true;
                    }

                    $next = array_shift($this->returns);
                    if ($next instanceof Constraint) {
                        $next->evaluate($actual);
                        return true;
                    }

                    return $actual === $next;
                }
            });
        }

        return $callbacks;
    }
}

