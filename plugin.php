  <?php

namespace Kanboard\Plugin\SendMailByCategory;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        // Registramos la acción automática
        $this->actionManager->register(new Action\SendMailOnFinalizedByCategory($this->container));
    }

    public function getPluginName()
    {
        return 'SendMailByCategory';
    }

    public function getPluginDescription()
    {
        return t('Send email to specific recipients based on task category when moved to a target column.');
    }

    public function getPluginAuthor()
    {
        return 'Tu equipo';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getCompatibleVersion()
    {
        // Ajustá si usás una versión muy vieja
        return '>=1.2.0';
    }
}
