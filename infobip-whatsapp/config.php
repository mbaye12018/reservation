<?php
// config.php

// Remplacez par votre nouvelle clé API Infobip
define('INFOBIP_API_KEY', 'ac6b0a32a15d86d1c3b6e8db0157ac8f-43269c9d-bdce-470c-ba7b-5d11ba275a37');

// Remplacez par votre numéro WhatsApp autorisé (format E.164 sans le préfixe 'whatsapp:')
define('INFOBIP_SENDER_NUMBER', '447860099299');

// Nom exact de votre template approuvé dans Infobip
define('INFIBIP_TEMPLATE_NAME', 'authentication');

// ID de modèle du template (si nécessaire)
define('INFIBIP_TEMPLATE_ID', '1548546645941697');

// URL de notification (optionnel)
define('INFIBIP_NOTIFY_URL', 'https://www.votresite.com/whatsapp_callback');

// URL de suivi (optionnel)
define('INFIBIP_TRACKING_URL', 'https://www.votresite.com/click_report');

// Domaine personnalisé (optionnel)
define('INFIBIP_CUSTOM_DOMAIN', 'votredomaine.com');
?>
