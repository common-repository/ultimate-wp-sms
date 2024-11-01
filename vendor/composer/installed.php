<?php return array(
    'root' => array(
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => NULL,
        'name' => 'uws/sms',
        'dev' => true,
    ),
    'versions' => array(
        'twilio/sdk' => array(
            'pretty_version' => '7.12.1',
            'version' => '7.12.1.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../twilio/sdk',
            'aliases' => array(),
            'reference' => '457122424aca03122239cf13c83cf170fca15443',
            'dev_requirement' => false,
        ),
        'uws/sms' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => NULL,
            'dev_requirement' => false,
        ),
    ),
);
