<?php

namespace App\Helper;

use App\Entity\Address;
use App\Entity\Tracking;
use App\Helper\EntityFactory;
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
        TruckerRepository $truckerRepository,
        AddressRepository $addressRepository,
        EntityManagerInterface $entityManager
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

    public function getAddress($addressData)
    {
        $addressData->street_number . ', '
            . $addressData->street_name . ', '
            . $addressData->neighborhood . ', '
            . $addressData->city . ', '
            . $addressData->state . ', '
            . $addressData->zip_code;

        $options = [
            CURLOPT_CONNECTTIMEOUT => 2, 
            CURLOPT_SSL_VERIFYPEER => false,
        ];

        $address = $this->addressRepository->findOneBy(
            (array)$addressData
        );

        if(!$address){
            $adapter  = new Client(null, null, $options);
            $geocoder = new LocationIQ($adapter, $_ENV['LOCATION_IQ']);

            $coordinates = $geocoder
                ->geocodeQuery(GeocodeQuery::create($fromAddress))
                ->first()
                ->getCoordinates();

            $latitude = $coordinates->getLatitude();
            $longitude = $coordinates->getLongitude();

            $address = new Address();
            $address
                ->setStreetName($newTrackingData->from->street_name)
                ->setStreetNumber($newTrackingData->from->street_number)
                ->setNeighborhood($newTrackingData->from->neighborhood)
                ->setZipCode($newTrackingData->from->zip_code)
                ->setCity($newTrackingData->from->city)
                ->setState($newTrackingData->from->state)
                ->setLatitude($fromLatitude)
                ->setLongitude($fromLongitude);

            $this->entityManager->persist($fromAddress);
            $this->entityManager->flush();
        }

        return $address;
    }
}
