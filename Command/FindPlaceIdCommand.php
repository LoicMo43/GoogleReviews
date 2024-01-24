<?php

namespace GoogleReviews\Command;

use GoogleReviews\Service\GoogleApiService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thelia\Command\ContainerAwareCommand;

class FindPlaceIdCommand extends ContainerAwareCommand
{
    public function __construct(protected GoogleApiService $googleApiService)
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setName("module:GoogleReviews:getPlaceId")
            ->setDescription("Get place id from latitude, longitude, fieldType and field")
            ->addArgument('lat')
            ->addArgument('lng')
            ->addArgument('field')
         ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $results = $this->googleApiService->findPlaceId(
            $input->getArgument('lat'),
            $input->getArgument('lng'),
            $input->getArgument('field')
        );

        $output->write("place_id find : \n");

        foreach ($results as $result)
        {
            $output->write($result["place_id"] . "\n");
        }

        return 0;
    }
}