<?php

declare(strict_types=1);

class PropertyHook
{
    private bool $modified = false;

    /** @param string $example this is my property */
    public function __construct(
        /** @var string this is my property */
        #[Property(new DateTimeImmutable())]
        public string $example = 'default value' {
            /** Not sure this works, but it gets */
            #[Getter(new DateTimeImmutable())]
            get {
                if ($this->modified) {
                    return $this->foo . ' (modified)';
                }
                return $this->foo;
            }
            /** Not sure this works, but it sets */
            #[Setter(new DateTimeImmutable())]
            set(string|int $value) {
                $this->foo = strtolower($value);
                $this->modified = true;
            }
        }
    )
    {
    }
}
