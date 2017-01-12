<?php


use Yosymfony\Spress\Core\Plugin\PluginInterface;
use Yosymfony\Spress\Core\Plugin\EventSubscriber;
use Yosymfony\Spress\Core\Plugin\Event\EnvironmentEvent;
use Yosymfony\Spress\Core\Plugin\Event\ContentEvent;
use Yosymfony\Spress\Core\Plugin\Event\FinishEvent;
use Yosymfony\Spress\Core\Plugin\Event\RenderEvent;

class CpTwitterembed implements PluginInterface
{
    private $io;

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
            'name' => 'cp/twitterembed',
            'description' => 'Embed tweets with the URL',
            'author' => 'Christian Lawson-Perfect',
            'license' => 'MIT',
        ];
    }
    
    public function onStart(EnvironmentEvent $event)
    {
        $this->io = $event->getIO();

		$renderizer = $event->getRenderizer();

		$renderizer->addTwigFilter('tweets',function($text) {
			return $this->embedTweets($text);
		});
    }

	private $tweet_pattern = '#<p>(https://twitter.com/(\S+)/status/(\d+))</p>#m';

	public function embedTweets($text) {
		$text = preg_replace_callback($this->tweet_pattern,function($matches) {
			return "\n<blockquote class=\"twitter-tweet\" data-conversation=\"none\" data-id=\"$matches[3]\"><a href=\"$matches[1]\">$matches[1]</a></blockquote>\n";
		},$text);
		return $text;
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

    }
}
