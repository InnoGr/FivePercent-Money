<?php

/**
 * This file is part of the Money package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Money;

/**
 * Immutable Money (every operation returns new instance) for working with finances.
 *
 * @author Dmitry Krasun <krasun.net@gmail.com>
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Money
{
    /**
     * All operations work and should work only with specified precision.
     */
    const PRECISION = 4;

    /**
     * @var string
     */
    private $value;

    /**
     * Construct. Create a new Money instance
     *
     * @param float|Money $value
     */
    public function __construct($value)
    {
        if ($value instanceof Money) {
            $value = $value->getValue();
        }

        if (!is_string($value)) {
            $value = $this->format($value);
        }

        $this->value = $value;
    }

    /**
     * Base function for create money instance via constructor
     *
     * @param float|Money $value
     *
     * @return Money
     */
    public static function create($value)
    {
        return new static($value);
    }

    /**
     * Create Money instance from cents.
     *
     * @param int $cents
     *
     * @return Money
     */
    public static function fromCents($cents)
    {
        return new static($cents / 100);
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Multiplies and returns new instance.
     *
     * @param Money|float|integer|string $value
     *
     * @return Money
     */
    public function mul($value)
    {
        return new static(
            bcmul($this->getValue(), (new Money($value))->getValue(), static::PRECISION)
        );
    }

    /**
     * Divides and returns new instance.
     *
     * @param Money|float|integer|string $value
     *
     * @return Money
     */
    public function div($value)
    {
        return new static(
            bcdiv($this->getValue(), (new Money($value))->getValue(), static::PRECISION)
        );
    }

    /**
     * Subtracts and returns new instance.
     *
     * @param Money|float|integer|string $value
     *
     * @return Money
     */
    public function sub($value)
    {
        return new static(
            bcsub($this->getValue(), (new Money($value))->getValue(), static::PRECISION)
        );
    }

    /**
     * Converts to absolute value.
     *
     * @return Money
     */
    public function abs()
    {
        return $this->toPositive();
    }

    /**
     * Adds and returns new instance.
     *
     * @param Money|float|integer|string $value
     *
     * @return Money
     */
    public function add($value)
    {
        return new static(
            bcadd($this->getValue(), (new Money($value))->getValue(), static::PRECISION)
        );
    }

    /**
     * Determines is value zero or negative.
     *
     * @return bool
     */
    public function isZeroOrNegative()
    {
        return $this->isZero() or $this->isNegative();
    }

    /**
     * Determines is value zero or positive.
     *
     * @return bool
     */
    public function isZeroOrPositive()
    {
        return $this->isZero() or $this->isPositive();
    }


    /**
     * Determines is value negative.
     *
     * @return bool
     */
    public function isNegative()
    {
        return bccomp($this->getValue(), '0', self::PRECISION) < 0;
    }

    /**
     * Determines is value positive
     *
     * @return bool
     */
    public function isPositive()
    {
        return bccomp($this->getValue(), '0', self::PRECISION) > 0;
    }

    /**
     * Determines is value zero.
     *
     * @return bool
     */
    public function isZero()
    {
        return bccomp($this->getValue(), '0', self::PRECISION) == 0;
    }

    /**
     * Convert value to negative
     *
     * @return Money
     */
    public function toNegative()
    {
        if ($this->isNegative()) {
            return clone $this;
        }

        return $this->mul(-1);
    }

    /**
     * Convert value to positive
     *
     * @return Money
     */
    public function toPositive()
    {
        if ($this->isPositive()) {
            return clone $this;
        }

        return $this->mul(-1);
    }

    /**
     * Get double value
     *
     * @param int $precision
     *
     * @return double
     */
    public function toDouble($precision = self::PRECISION)
    {
        return (double) $this->format($this->value, $precision);
    }

    /**
     * Get money with cents (989 as example - 9.89)
     *
     * @return int
     */
    public function toCents()
    {
        return (double) $this->value * 100;
    }

    /**
     * @param Money $another
     *
     * @return bool
     */
    public function greaterThan(Money $another)
    {
        return bccomp($this->getValue(), $another->getValue(), self::PRECISION) > 0;
    }

    /**
     * Formatting value
     *
     * @param mixed $value
     * @param int   $precision
     *
     * @return string
     */
    protected function format($value, $precision = self::PRECISION)
    {
        return number_format($value, $precision, '.', '');
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }
}
