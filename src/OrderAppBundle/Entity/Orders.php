<?php

namespace OrderAppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="OrderAppBundle\Repository\OrdersRepository")
 */
class Orders
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Orders constructor.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->status = true;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Clients", inversedBy="orders")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @ORM\ManyToMany(targetEntity="Products", inversedBy="orders")
     *
     */
    private $products;

    /**
     * @ORM\Column(name="full_order", type="text", length=2500)
     */
    private $fullOrder;

    /**
     * @ORM\Column(name="total_price", type="float")
     */
    private $totalPrice;

    /**
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @return mixed
     */
    public function getFullOrder()
    {
        return $this->fullOrder;
    }

    /**
     * @param mixed $fullOrder
     */
    public function setFullOrder($fullOrder)
    {
        $this->fullOrder = $fullOrder;
    }

    /**
     * Add products
     *
     * @param \OrderAppBundle\Entity\Products $products
     * @return Orders
     */
    public function addProduct(\OrderAppBundle\Entity\Products $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \OrderAppBundle\Entity\Products $products
     */
    public function removeProduct(\OrderAppBundle\Entity\Products $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * Set totalPrice
     *
     * @param float $totalPrice
     * @return Orders
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return float 
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Orders
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
