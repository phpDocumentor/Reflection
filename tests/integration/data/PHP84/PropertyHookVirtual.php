<?php

declare(strict_types=1);

class PropertyHookVirtual
{
    /**
     * A virtual property that composes a full name from first and last name
     */
    public string $fullName {
        // This is a virtual property with a getter
        // It doesn't reference $this->fullName
        get {
            return $this->firstName . ' ' . $this->lastName;
        }
    }

    /**
     * A virtual property that decomposes a full name into first and last name
     */
    public string $compositeName {
        // This is a virtual property with a setter
        // It doesn't reference $this->compositeName
        set(string $value) {
            [$this->firstName, $this->lastName] = explode(' ', $value, 2);
        }
    }

    /**
     * A virtual property with both getter and setter
     */
    public string $completeFullName {
        // Getter doesn't reference $this->completeFullName
        get {
            return $this->firstName . ' ' . $this->lastName;
        }
        // Setter doesn't reference $this->completeFullName
        set(string $value) {
            [$this->firstName, $this->lastName] = explode(' ', $value, 2);
        }
    }

    /**
     * A non-virtual property that references itself in its hook
     */
    public string $nonVirtualName {
        get {
            return $this->nonVirtualName ?? $this->firstName;
        }
        set(string $value) {
            $this->nonVirtualName = $value;
        }
    }

    public function __construct(
        private string $firstName = 'John',
        private string $lastName = 'Doe'
    ) {
    }
}
