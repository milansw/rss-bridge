<?php
/**
* RssBridgeIdentica 
* 2014-05-25
*
* @name Identica Bridge
* @homepage https://identi.ca/
* @description Returns user timelines
* @maintainer mitsukarenai
* @use1(u="username")
*/
class IdenticaBridge extends BridgeAbstract{
	
	private $request;

    public function collectData(array $param){
        $html = '';
        if (isset($param['u'])) {   /* user timeline mode */
        	$this->request = $param['u'];
            $html = file_get_html('https://identi.ca/'.urlencode($this->request)) or $this->returnError('Requested username can\'t be found.', 404);
        }
        else {
            $this->returnError('You must specify an Identica username (?u=...).', 400);
        }

        foreach($html->find('li.major') as $dent) {
            $item = new \Item();
            $item->uri = html_entity_decode($dent->find('a', 0)->href);	// get dent link
            $item->timestamp = strtotime($dent->find('abbr.easydate', 0)->plaintext);	// extract dent timestamp
            $item->content = trim($dent->find('div.activity-content', 0)->innertext);	// extract dent text
            $item->title = $param['u'] . ' | ' . $item->content;
            $this->items[] = $item;
        }
    }

    public function getName(){
        return (!empty($this->request) ? $this->request .' - ' : '') .'Identica Bridge';
    }

    public function getURI(){
        return 'https://identica.com';
    }

    public function getCacheDuration(){
        return 300; // 5 minutes
    }
}
