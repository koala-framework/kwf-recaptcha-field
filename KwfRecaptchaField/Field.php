<?php
class KwfRecaptchaField_Field extends Kwf_Form_Field_Abstract
{
    public function __construct($fieldName = 'recaptcha', $fieldLabel = null)
    {
        parent::__construct($fieldName, $fieldLabel);
        $this->setSave(false);
    }

    public function validate($row, $postData)
    {
        $ret = parent::validate($row, $postData);

        $error = array('message' => trlKwf('Something went wrong'));
        $value = $postData[$this->getFieldName()];
        if (!$value) {
            $ret[] = $error;
            return $ret;
        }

        $client = $this->_getClient();
        $client->setMethod(Zend_Http_Client::POST);
        $client->setParameterPost(array(
            'secret' => Kwf_Config::getValue('kwfRecaptchaField.secret'),
            'response' => $value
        ));
        $response = $client->request();

        if (!$response->isSuccessful()) {
            $ret[] = $error;
            return $ret;
        }

        $data = json_decode($response->getBody(), true);
        if (!$data['success']) {
            $ret[] = $error;
            return $ret;
        }

        return $ret;
    }

    public function getFrontendMetaData()
    {
        $ret = parent::getFrontendMetaData();
        $ret['key'] = Kwf_Config::getValue('kwfRecaptchaField.key');
        $ret['callback'] = $this->getCallback();
        $ret['actionName'] = $this->getActionName();
        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);

        $name = htmlspecialchars($this->getFieldName() . $fieldNamePostfix);
        $callback = "GoogleReCaptchaLoaded" . str_replace(array('-','_'), '', $idPrefix);
        $this->setCallback($callback);
        if (!$this->getActionName()) $this->setActionName($idPrefix . $name);

        $ret['html'] = "<input type=\"hidden\" name=\"{$name}\" />";
        return $ret;
    }

    /**
     * Note: actions may only contain alphanumeric characters and slashes, and must not be user-specific.
     */
    public function setActionName($value)
    {
        return $this->setProperty('actionName', preg_replace('#[^a-z0-9]+#i', '', $value));
    }

    public function setCallback($value)
    {
        return $this->setProperty('callback', $value);
    }

    protected function _getClient()
    {
        $config = array(
            'adapter' => 'Zend_Http_Client_Adapter_Curl'
        );
        if (Kwf_Config::getValue('http.proxy.host')) {
            $config['proxy_host'] = Kwf_Config::getValue('http.proxy.host');
            $config['proxy_port'] = Kwf_Config::getValue('http.proxy.port');
        }
        return new Zend_Http_Client('https://www.google.com/recaptcha/api/siteverify', $config);
    }
}
