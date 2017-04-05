<?php


use Yosymfony\Spress\Core\Plugin\PluginInterface;
use Yosymfony\Spress\Core\Plugin\EventSubscriber;
use Yosymfony\Spress\Core\Plugin\Event\EnvironmentEvent;
use Yosymfony\Spress\Core\Plugin\Event\ContentEvent;
use Yosymfony\Spress\Core\Plugin\Event\FinishEvent;
use Yosymfony\Spress\Core\Plugin\Event\RenderEvent;
use Yosymfony\Spress\Core\DataSource\ItemInterface;

class CpChanges implements PluginInterface
{
	private $io;
	private $last_commit_filename = 'last-commit.txt';

    public function initialize(EventSubscriber $subscriber)
    {
        $subscriber->addEventListener('spress.start', 'onStart');
        $subscriber->addEventListener('spress.before_convert', 'onBeforeConvert');
        $subscriber->addEventListener('spress.after_convert', 'onAfterConvert');
        $subscriber->addEventListener('spress.before_render_blocks', 'onBeforeRenderBlocks');
        $subscriber->addEventListener('spress.after_render_blocks', 'onAfterRenderBlocks');
        $subscriber->addEventListener('spress.before_render_page', 'onBeforeRenderPage');
        $subscriber->addEventListener('spress.after_render_page', 'onAfterRenderPage');
        $subscriber->addEventListener('spress.finish', 'onFinish');
    }

    public function getMetas()
    {
        return [
            'name' => 'cp/changes',
            'description' => 'changes',
            'author' => 'cp',
            'license' => 'MIT',
        ];
    }
    
    public function onStart(EnvironmentEvent $event)
    {
        $this->io = $event->getIO();
		$last_log = trim(`git log --oneline -n 1`);
		$current_commit = explode(' ',$last_log)[0];
		$this->commit_message = substr($last_log,strlen($current_commit)+1);
		$this->io->write($current_commit);

		if(!(file_exists($this->last_commit_filename) && $last_commit=trim(file_get_contents($this->last_commit_filename)))) {
			$this->io->write("No last commit file.");
		} else {
			$this->io->write('last commit '.$last_commit);
		}

		$cmd = "git diff $last_commit..HEAD --name-status --oneline | grep -P '[AM]\\t(.*)' | sed 's/[AM]\\t\\(.*\\)/\\1/'";
		$changed_files = shell_exec($cmd);
		$changed_files = explode("\n",$changed_files);
		$this->changed_files = array();
		foreach($changed_files as $f) {
			$f = str_replace('src/content/','',$f);
			$this->changed_files[] = $f;
			$this->io->write($f);
		}

		file_put_contents($this->last_commit_filename,$current_commit);
    }

    public function onBeforeConvert(ContentEvent $event)
    {

    }

    public function onAfterConvert(ContentEvent $event)
    {

    }

    public function onBeforeRenderBlocks(RenderEvent $event)
    {

    }

    public function onAfterRenderBlocks(RenderEvent $event)
    {

    }

    public function onBeforeRenderPage(RenderEvent $event)
    {

    }

    public function onAfterRenderPage(RenderEvent $event)
    {

    }

    public function onFinish(FinishEvent $event)
    {
		$siteAttributes = $event->getSiteAttributes();
		$site_url = $siteAttributes['site']['url'];

		$items = $event->getItems();

		$this->changed_urls = array();
		foreach($items as $item) {
			$source = $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE);
			if($item->getCollection()=='posts' && in_array($source,$this->changed_files)) {
				$url = $site_url . '/' . preg_replace('#/index.html$#','',$item->getPath());
				$this->changed_urls[] = $url;
			}
		}

		if(count($this->changed_urls)==1 && substr($this->commit_message,0,1)!='!') {
			$message = substr($this->commit_message,0,120)."\n".$this->changed_urls[0];
			$this->io->write($message);

			$tw = $siteAttributes['site']['twitter'];
			$this->twitter = new Twitter($tw['consumer_key'], $tw['consumer_secret'], $tw['access_token'], $tw['access_token_secret']);

			$this->twitter->send($message);
		}
    }
}
