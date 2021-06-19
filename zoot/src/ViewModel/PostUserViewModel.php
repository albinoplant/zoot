<?php


namespace App\ViewModel;


class PostUserViewModel
{
    private string $username;
    private bool $isVet;

    /**
     * @return bool
     */
    public function isVet(): bool
    {
        return $this->isVet;
    }

    /**
     * @param bool $isVet
     */
    public function setIsVet(bool $isVet): void
    {
        $this->isVet = $isVet;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
}