<?php

declare(strict_types=1);

class PropertyHook
{
    private bool $modified = false;

    /** @var string this is my property */
    #[Property(new DateTimeImmutable())]
    public private(set) string $example = 'default value' {
        get {
            if ($this->modified) {
                return $this->foo . ' (modified)';
            }
            return $this->foo;
        }
        set(string|int $value) {
            $this->foo = strtolower($value);
            $this->modified = true;
        }
    }
}
