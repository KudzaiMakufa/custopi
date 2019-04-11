<?php

declare(strict_types=1);
namespace PhpmlExamples;
use Phpml\Dataset\CsvDataset;
use Phpml\Dataset\ArrayDataset;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Metric\Accuracy;
use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;

class Controller_Review
{


	public function action_index()
	{


        //Debug::dump( get_declared_classes());die;
        $file = DOCROOT.'/docs/test.csv';
        $dataset = new CsvDataset($file);
        $vectorizer = new TokenCountVectorizer(new WordTokenizer());
        $tfIdfTransformer = new TfIdfTransformer();
        $samples = [];
        foreach ($dataset->getSamples() as $sample) {
            $samples[] = $sample[0];
        }
        $vectorizer->fit($samples);
        $vectorizer->transform($samples);
        $tfIdfTransformer->fit($samples);
        $tfIdfTransformer->transform($samples);
        $dataset = new ArrayDataset($samples, $dataset->getTargets());
        $randomSplit = new StratifiedRandomSplit($dataset, 0.1);
        $classifier = new SVC(Kernel::RBF, 10000);
        $classifier->train($randomSplit->getTrainSamples(), $randomSplit->getTrainLabels());
        $predictedLabels = $classifier->predict($randomSplit->getTestSamples());
        echo 'Accuracy: '.Accuracy::score($randomSplit->getTestLabels(), $predictedLabels);
		

		$this->template->title = "Leaves";
		$this->template->content = View::forge('prochem/test');
	}
}
