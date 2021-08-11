<?php

namespace App\Command;

use App\Omdb\OmdbClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImdbSearchCommand extends Command
{
    protected static $defaultName = 'app:imdb:search';
    protected static $defaultDescription = 'IMDB search engine as CLI command';

    /**
     * @var OmdbClient
     */
    private $omdbClient;

    public function __construct(OmdbClient $omdbClient)
    {
        $this->omdbClient = $omdbClient;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('keyword', InputArgument::OPTIONAL, 'Keyword to find movies')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $keyword = $input->getArgument('keyword');

        if (!$keyword) {
            $keyword = $io->ask('Which movie you want to search?', 'Matrix', function($answer) {
                $answer = strtolower($answer);
                $forbiddenKeywords = ['shit', 'hassle'];
                foreach ($forbiddenKeywords as $keyword) {
                    if (false !== strpos($answer, $keyword)) {
                        throw new \InvalidArgumentException('Your search is not correct, please try an other :).');
                    }
                }

                return $answer;
            });
        }

        $search = $this->omdbClient->requestBySearch($keyword);
        $io->progressStart(count($search['Search']));
        $rows = array_map(function($movie) use ($io) {
            usleep(100000);
            $io->progressAdvance(1);
            $movie['URL'] = 'https://www.imdb.com/title/' . $movie['imdbID'].'/';

            return [$movie['Title'], $movie['Year'], $movie['Type'], $movie['URL']];
        }, $search['Search']);
        $output->write("\r");
        //$io->progressFinish();

        $io->title(sprintf('%d movies found for your search: "%s"', $search['totalResults'], $keyword));
        $io->table(['Title', 'Year', 'Type', 'URL'], $rows);
        //dump($search);


        return Command::SUCCESS;
    }
}
