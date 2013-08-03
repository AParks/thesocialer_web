<?php

class Community extends ApplicationBase {

    public function execute() {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    foreach ($_POST['photos'] as $photo)
                        $this->upLoadPopUpPhotos($photo['url']);
                }
        
        $x = XSLTransformer::getInstance();
        $this->assetsManager->addCSS('Community');
        $this->assetsManager->addJavascript('filepicker');
        $this->assetsManager->addJavascript('Community');
        $this->assetsManager->addInitJavaScript("$('.NavigationLink.fourth').addClass('active');");


        $node = $this->dom->createElement('Community');
        $node->appendChild($this->getPopUpPhotos());
        $node->appendChild($this->getLoggedInMemberNode());

        
        
        
        
        
        $output = $x->transform('Community', $node);
        $this->display->appendOutput($output);
    }
  

    function upLoadPopUpPhotos($path){
         $query = sPDO::getInstance()->prepare("INSERT into popup_photos(path) values(:path)");
         $query->bindValue(':path', $path);
         $query->execute();
       
    }
    function getPopUpPhotos() {
        $query = sPDO::getInstance()->prepare("SELECT path FROM popup_photos");
        $query->execute();
        $photos = $this->dom->createElement('Photos');

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $photo) {
            $photo_node = $this->dom->createElement('Photo');
            $photo_node->setAttribute('path',$photo['path']);
            $photos->appendChild($photo_node);
        }
        return $photos;
    }

    
}
