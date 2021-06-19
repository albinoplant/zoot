<?php


namespace App\ViewModel;


class PostAppointmentViewModel
{
    private \DateTime $date;

    private string $description;

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    /**
     * @return int
     */
    public function getAnimalId(): int
    {
        return $this->animalId;
    }

    /**
     * @param int $animalId
     */
    public function setAnimalId(int $animalId): void
    {
        $this->animalId = $animalId;
    }

    private int $animalId;
}