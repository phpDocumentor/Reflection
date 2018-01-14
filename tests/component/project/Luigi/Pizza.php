<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace Luigi;

class Pizza extends \Pizza
{
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
