<?php
// @codingStandardsIgnoreStart
/**
 * Summary of the File DocBlock.
 *
 * Description of the File.
 *
 * @package Luigi\Pizza
 * @author Mike van Riel <me@mikevanriel.com>
 */

namespace Luigi\Pizza
{
    /**
     * The high VAT percentage.
     *
     * This describes the VAT percentage for all non-food items.
     *
     * @var integer
     */
    const VAT_HIGH = 21;

    /**
     * The low VAT percentage.
     *
     * This describes the VAT percentage for all non-food items.
     *
     * @var integer
     */
    define('Luigi\Pizza\VAT_LOW', 6);

    /**
     * @var integer SIZE_5CM     A 5 centimeter pizza size.
     * @var integer SIZE_10CM    A 10 centimeter pizza size.
     * @var integer SIZE_15CM    A 15 centimeter pizza size.
     * @var integer SIZE_20CM    A 20 centimeter pizza size.
     * @var integer DEFAULT_SIZE The default Pizza size if you don't provide your own.
     */
    const SIZE_5CM = 5, SIZE_10CM = 10, SIZE_15CM = 15, SIZE_20CM = 20, DEFAULT_SIZE = SIZE_20CM;

    trait ExampleNestedTrait
    {
        private function exampleTraitMethod()
        {
        }
    }

    /**
     * A single item with a value
     *
     * Represents something with a price.
     */
    trait HasPrice
    {
        use ExampleNestedTrait;

        private $temporaryPrice = 1;

        public function getPrice()
        {
            return $this->price;
        }
    }

    /**
     * Any class implementing this interface has an associated price.
     *
     * Using this interface we can easily add the price of all components in a pizza by checking for this interface and
     * adding the prices together for all components.
     */
    interface Valued
    {
        const BASE_PRICE = 1;

        public function getPrice();
    }

    interface Series
    {
    }

    interface Style extends Valued
    {
    }

    interface Sauce extends Valued
    {
    }

    interface Topping extends Valued, \Serializable
    {
    }

    abstract class PizzaComponentFactory implements \Traversable, Valued
    {
        public function add()
        {
        }

        /**
         * Calculates the price for this specific component.
         *
         * @return float
         */
        abstract protected function calculatePrice();
    }

    final class StyleFactory extends PizzaComponentFactory
    {
        public function getPrice()
        {
        }

        protected function calculatePrice()
        {
        }
    }

    final class SauceFactory extends PizzaComponentFactory
    {
        public function getPrice()
        {
        }

        protected function calculatePrice()
        {
        }
    }

    final class ToppingFactory extends PizzaComponentFactory
    {
        public function getPrice()
        {
        }

        protected function calculatePrice()
        {
        }
    }

    final class ItalianStyle implements Style
    {
        use HasPrice;

        private $price = 2.0;
    }

    final class AmericanStyle implements Style
    {
        use HasPrice;

        private $price = 1.5;
    }

    final class TomatoSauce implements Sauce
    {
        use HasPrice;

        private $price = 1.5;
    }

    final class CheeseTopping implements Topping
    {
        use HasPrice;

        private $price = 1.5;

        public function serialize()
        {
        }

        public function unserialize($serialized)
        {
        }
    }
}

namespace Luigi
{
    /**
     * Class representing a single Pizza.
     *
     * This is Luigi's famous Pizza.
     *
     * @package Luigi\Pizza
     */
    class Pizza implements Pizza\Valued
    {
        /**
         * The packaging method used to transport the pizza.
         */
        const PACKAGING = 'box';

        const
            /** @var string DELIVERY designates that the delivery method is to deliver the pizza to the customer. */
            DELIVERY = 'delivery';

        const /** @var string PICKUP   designates that the delivery method is that the customer picks the pizza up. */
            PICKUP = 'pickup';

        /** @var static contains the active instance for this Pizza. */
        private static $instance;

        /**
         * @var Pizza\Style
         * @var Pizza\Sauce|null $sauce
         * @var Pizza\Topping[]  $toppings
         */
        private $style;

        private $sauce;

        private $toppings;

        /**
         * The size of the pizza in centimeters, defaults to 20cm.
         *
         * @var int
         */
        public $size = \Luigi\Pizza\SIZE_20CM;

        public $legacy; // don't use this anymore!

        protected $packaging = self::PACKAGING;

        protected $deliveryMethod;

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
         * @see self::getInstance to retrieve the pizza object.
         */
        public static function createInstance(Pizza\Style $style)
        {
            self::$instance = new static($style);
        }

        /**
         * @return self
         */
        public static function getInstance()
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
}
// @codingStandardsIgnoreEnd
