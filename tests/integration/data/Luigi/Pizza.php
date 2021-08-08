<?php
// phpcs:ignoreFile
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace Luigi;

class Pizza extends \Pizza
{
    const
        /** @var string DELIVERY designates that the delivery method is to deliver the pizza to the customer. */
        DELIVERY = 'delivery',
        /** @var string PICKUP   designates that the delivery method is that the customer picks the pizza up. */
        PICKUP = 'pickup';

    use ExampleNestedTrait;

    /** @var static contains the active instance for this Pizza. */
    static private $instance;

    /**
     * @var Pizza\Style      $style
     * @var Pizza\Sauce|null $sauce
     * @var Pizza\Topping[]  $toppings
     */
    private $style, $sauce, $toppings;

    /**
     * The size of the pizza in centimeters, defaults to 20cm.
     */
    public int $size = \Luigi\Pizza\SIZE_20CM;

    var $legacy; // don't use this anymore!

    protected
        /** @var string $packaging The type of packaging for this Pizza */
        $packaging = self::PACKAGING,
        /** @var string $deliveryMethod Is the customer picking this pizza up or must it be delivered? */
        $deliveryMethod;

    private function __construct(Pizza\Style $style)
    {
        $this->style = $style;
    }

    /**
     * Creates a new instance of a Pizza.
     *
     * This method can be used to instantiate a new object of this class which can then be retrieved using
     * {@see self::getInstance()}.
     *
     * @param Pizza\Style $style
     *
     * @see self::getInstance to retrieve the pizza object.
     *
     * @return void
     */
    public static function createInstance(Pizza\Style $style)
    {
        self::$instance = new static($style);
    }

    /**
     * @return self
     */
    static function getInstance()
    {
        return self::$instance;
    }

    final public function setSauce(Pizza\Sauce $sauce)
    {
        $this->sauce = $sauce;
    }

    final public function addTopping(Pizza\Topping $topping)
    {
        $this->toppings[] = $topping;
    }

    public function setSize(&$size = \Luigi\Pizza\SIZE_20CM)
    {
    }

    public function getPrice()
    {
    }
}
