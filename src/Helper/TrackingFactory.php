<?php

namespace App\Helper;

use App\Entity\Address;
use App\Helper\EntityFactory;
use App\Entity\Tracking;
use App\Repository\AddressRepository;
use App\Repository\TruckerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Geocoder\Provider\LocationIQ\LocationIQ;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Http\Client\Curl\Client;

class TrackingFactory implements EntityFactory
{
    /**
     * @var TruckerRepository
     */
    private $truckerRepository;
    /**
     * @var AddressRepository
     */
    private $addressRepository;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TruckerRepository $truckerRepository
     * @var AddressRepository $addressRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TruckerRepository $truckerRepository,
        AddressRepository $addressRepository
    ) {
        $this->entityManager = $entityManager;
        $this->truckerRepository = $truckerRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param  string $json
     * @return Tracking
     */
    public function create(string $json)
    {
        $newTrackingData = json_decode($json);
        $trucker = $this->truckerRepository->find($newTrackingData->trucker_id);

        if(!$trucker) {
            throw new \Exception;
        }

        $fromAddress = $this->getAddress($newTrackingData->from);

        $toAddress = array_key_exists('to', $newTrackingData)
            ? $this->getAddress($newTrackingData->to)
            : $fromAddress;

        if(!$trucker->getIsLoaded()) {
            $toAddress = $fromAddress;
        }

        $tracking = new Tracking();
        $tracking
            ->setTrucker($trucker)
            ->setFromAddress($fromAddress)
            ->setToAddress($toAddress)
            ->setCheckIn($newTrackingData->check_in)
            ->setCheckOut($newTrackingData->check_out);

        return $tracking;
    }

    public function getAddress($newAddress)
    {
        $addressData = [
            'street_number' => $newAddress->street_number,
            'street_name'   => $newAddress->street_name,
            'neighborhood'  => $newAddress->neighborhood,
            'city'          => $newAddress->city,
            'state'         => $newAddress->state,
            'zip_code'      => $newAddress->zip_code
        ];

        $options = [
            CURLOPT_CONNECTTIMEOUT => 2, 
            CURLOPT_SSL_VERIFYPEER => false,
        ];

        $address = $this->addressRepository->findOneBy($addressData);

        if(!$address){
            $adapter  = new Client(null, null, $options);
            $geocoder = new LocationIQ($adapter, $_ENV['LOCATION_IQ']);

            $coordinates = $geocoder
                ->geocodeQuery(GeocodeQuery::create(
                    $newAddress->street_number . ', '
                    . $newAddress->street_name . ', '
                    . $newAddress->neighborhood . ', '
                    . $newAddress->city . ', '
                    . $newAddress->state . ', '
                    . $newAddress->zip_code
                ))
                ->first()
                ->getCoordinates();

            $latitude = $coordinates->getLatitude();
            $longitude = $coordinates->getLongitude();

            $address = new Address();
            $address
                ->setStreetName($newAddress->street_name)
                ->setStreetNumber($newAddress->street_number)
                ->setNeighborhood($newAddress->neighborhood)
                ->setZipCode($newAddress->zip_code)
                ->setCity($newAddress->city)
                ->setState($newAddress->state)
                ->setLatitude($latitude)
                ->setLongitude($longitude);

            $this->entityManager->persist($address);
            $this->entityManager->flush();
        }

        return $address;
    }
}
