<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Sequence\Validator;

class StringLength extends AbstractValidator
{
    const INVALID   = 'stringLengthInvalid';
    const TOO_SHORT = 'stringLengthTooShort';
    const TOO_LONG  = 'stringLengthTooLong';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID   => "Invalid type given. String expected",
        self::TOO_SHORT => "The input is less than %min% characters long",
        self::TOO_LONG  => "The input is more than %max% characters long",
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'min' => array('options' => 'min'),
        'max' => array('options' => 'max'),
    );

    protected $options = array(
        'min'      => 0,       // Minimum length
        'max'      => null,    // Maximum length, null if there is no length limitation
    );

    /**
     * Sets validator options
     *
     * @param  int|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $options     = func_get_args();
            $temp['min'] = array_shift($options);
			
            if (!empty($options)) {
                $temp['max'] = array_shift($options);
            }
			
            $options = $temp;
        }

        parent::__construct($options);
    }

    /**
     * Returns the min option
     *
     * @return int
     */
    public function getMin()
    {
        return $this->options['min'];
    }

    /**
     * Sets the min option
     *
     * @param  int $min
     * @throws Exception\InvalidArgumentException
     * @return StringLength Provides a fluent interface
     */
    public function setMin($min)
    {
        if (null !== $this->getMax() && $min > $this->getMax()) {
            throw new Exception\InvalidArgumentException('The minimum must be less than or equal to the maximum length, but '.$min.' >'.$this->getMax());
        }

        $this->options['min'] = max(0, (int) $min);
        return $this;
    }

    /**
     * Returns the max option
     *
     * @return int|null
     */
    public function getMax()
    {
        return $this->options['max'];
    }

    /**
     * Sets the max option
     *
     * @param  int|null $max
     * @throws Exception\InvalidArgumentException
     * @return StringLength Provides a fluent interface
     */
    public function setMax($max)
    {
        if (null === $max) {
            $this->options['max'] = null;
        } elseif ($max < $this->getMin()) {
            throw new Exception\InvalidArgumentException("The maximum must be greater than or equal to the minimum length, but "
                                            . "$max < " . $this->getMin());
        } else {
            $this->options['max'] = (int) $max;
        }

        return $this;
    }

    /**
     * Returns true if and only if the string length of $value is at least the min option and
     * no greater than the max option (when the max option is not null).
     *
     * @param  string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        $length = strlen($value);
        if ($length < $this->getMin()) {
            $this->error(self::TOO_SHORT);
        }

        if (null !== $this->getMax() && $this->getMax() < $length) {
            $this->error(self::TOO_LONG);
        }

        if (count($this->getMessages())) {
            return false;
        }

        return true;
    }
}
