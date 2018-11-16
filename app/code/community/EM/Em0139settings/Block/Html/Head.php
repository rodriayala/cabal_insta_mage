<?php
class EM_Em0139settings_Block_Html_Head extends Mage_Page_Block_Html_Head
{
    public function addEMResposiveRtlCss(){
        if (Mage::helper('themeframework/settings')->getGeneral_DisableResponsive() != 1) {
            if (Mage::helper('themeframework/settings')->getGeneral_RightToLeft() != 1) {
                $this->addItem('skin_css', 'css/bootstrap/noneresponsive.css');
            } else {
                $this->addItem('skin_css', 'css/bootstrap/noneresponsive-rtl.css');
                $this->addItem('skin_css', 'css/style-rtl.css');
            }
        } else {
            $this->addItem('skin_css', 'css/responsive.css');
            if (Mage::helper('themeframework/settings')->getGeneral_RightToLeft() != 1) {
                $this->addItem('skin_css', 'css/bootstrap/bootstrap.css');
            } else {
                $this->addItem('skin_css', 'css/bootstrap/bootstrap-rtl.css');
                $this->addItem('skin_css', 'css/style-rtl.css');
            }
        }
    }

    public function addEMItem($type, $name, $params = null, $if = null, $cond = null){
        if ($type == 'lesscss') {
            $_lessCacheId = 'em0139_less_cache_store_'.Mage::app()->getStore()->getCode();
    		$_newLessConfig = Mage::helper('em0139settings')->getAllCssConfig();
    		if (false !== ($data = Mage::app()->getCache()->load($_lessCacheId))) {
    			$_oldLessConfig = unserialize($data);
                if (count(array_diff_assoc($_oldLessConfig,$_newLessConfig)) == 0 && count(array_diff_assoc($_newLessConfig,$_oldLessConfig)) == 0) {
    				$_hasChange = false;
    			}else{
    				$_hasChange = true;
    				Mage::app()->getCache()->remove($_lessCacheId);
    				Mage::app()->getCache()->save(serialize($_newLessConfig), $_lessCacheId, array('EM0139_LESS_CSS_CACHE'), 86400);
    			}
    		} else {
    			$_hasChange = true;	
    			Mage::app()->getCache()->save(serialize($_newLessConfig), $_lessCacheId, array('EM0139_LESS_CSS_CACHE'), 86400);
    		}
            //echo $_hasChange;exit;
    		$pathFile = Mage::getBaseDir('media') . DS . 'em0139' . DS . 'css' . DS . 'less' . DS . 'variables_store_'.Mage::app()->getStore()->getCode().'.less';
    		if ($_hasChange = true || !file_exists($pathFile)) {
    			$stringless = '';
    			if (isset($_newLessConfig['@function_url'])) {
    				foreach (explode(',', $_newLessConfig['@function_url']) as $file) {
    					$stringless .= '@import ' . $file . ';';
    				}
    			}
    			if (isset($_newLessConfig['@variables_url'])) {
    				foreach (explode(',', $_newLessConfig['@variables_url']) as $file) {
    					$stringless .= '@import ' . $file . ';';
    				}
    			}
    			if (isset($_newLessConfig['additional_css_file'])) {
    				foreach ($_newLessConfig['additional_css_file'] as $file) {
    					$stringless .= '@import ' . $file . ';';
    				}
    			}
    			foreach ($_newLessConfig as $typo => $value) {
    				if ($typo != 'additional_css_file') {
    					$stringless .= $typo . ":" . $value . ';';
    				}
    			}
    			file_put_contents($pathFile, $stringless);
    		}
            $href = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'em0139/css/less/variables_store_'.Mage::app()->getStore()->getCode().'.less';
            $this->addItem('link_rel', $href, 'rel="stylesheet/less" type="text/css"');
        } else {
            if ($type == 'em_js') {
                $href = $this->getSkinUrl($name);
                $this->addItem($type, $href, $params, $if, $cond);
            } else {
                if ($if != null) {
                    if (strcmp('configswatches_general_enabled', $if) == 0) {
                        if (Mage::getStoreConfig('configswatches/general/enabled') == $cond) {
                            $this->addItem($type, $name);
                        }
                    } else {
                        if (Mage::helper('themeframework/managetheme')->getConfigTheme($if) == $cond) {
                                $this->addItem($type, $name);
                            }
                    }
                } else
                    $this->addItem($type, $name, $params, $if, $cond);
            }
        }
    }

    protected function _separateOtherHtmlHeadElements(&$lines, $itemIf, $itemType, $itemParams,$itemName, $itemThe){
        $params = $itemParams ? ' ' . $itemParams : '';
        $href = $itemName;
        switch ($itemType) {
            case 'rss':
                $lines[$itemIf]['other'][] = sprintf('<link href="%s"%s rel="alternate" type="application/rss+xml" />',
                    $href, $params);
                break;
            case 'link_rel':
                $lines[$itemIf]['other'][] = sprintf('<link%s href="%s" />', $params, $href);
                break;
            case 'em_js':
                $lines[$itemIf]['other'][] = sprintf('<script type="text/javascript" src="%s" %s></script>',
                    $href, $params);
                break;
        }
    }
}
