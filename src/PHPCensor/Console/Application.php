<?php

namespace PHPCensor\Console;

use b8\Config;
use b8\Store\Factory;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPCensor\Command\CreateAdminCommand;
use PHPCensor\Command\CreateBuildCommand;
use PHPCensor\Command\InstallCommand;
use PHPCensor\Command\RebuildCommand;
use PHPCensor\Command\RebuildQueueCommand;
use PHPCensor\Command\RunCommand;
use PHPCensor\Command\ScheduleBuildCommand;
use PHPCensor\Command\WorkerCommand;
use PHPCensor\Logging\Handler;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\Store\UserStore;
use Symfony\Component\Console\Application as BaseApplication;
use Phinx\Console\Command\Create;
use Phinx\Console\Command\Migrate;
use Phinx\Console\Command\Rollback;
use Phinx\Console\Command\Status;
use Phinx\Config\Config as PhinxConfig;

/**
 * Class Application
 *
 * @package PHPCensor\Console
 */
class Application extends BaseApplication
{
    const LOGO = <<<'LOGO'
    ____  __  ______    ______
   / __ \/ / / / __ \  / ____/__  ____  _________  _____
  / /_/ / /_/ / /_/ / / /   / _ \/ __ \/ ___/ __ \/ ___/
 / ____/ __  / ____/ / /___/  __/ / / (__  ) /_/ / /
/_/   /_/ /_/_/      \____/\___/_/ /_/____/\____/_/


LOGO;

    /**
     * @param Config $applicationConfig
     *
     * @return Logger
     */
    protected function initLogger(Config $applicationConfig)
    {
        $rotate   = (bool)$applicationConfig->get('php-censor.log.rotate', false);
        $maxFiles = (int)$applicationConfig->get('php-censor.log.max_files', 0);

        $loggerHandlers = [];
        if ($rotate) {
            $loggerHandlers[] = new RotatingFileHandler(RUNTIME_DIR . 'console.log', $maxFiles, Logger::DEBUG);
        } else {
            $loggerHandlers[] = new StreamHandler(RUNTIME_DIR . 'console.log', Logger::DEBUG);
        }

        $logger = new Logger('php-censor', $loggerHandlers);
        Handler::register($logger);

        return $logger;
    }

    /**
     * Constructor.
     *
     * @param string $name    The name of the application
     * @param string $version The version of the application
     */
    public function __construct($name = 'PHP Censor', $version = 'UNKNOWN')
    {
        $version = trim(file_get_contents(ROOT_DIR . 'VERSION.md'));

        parent::__construct($name, $version);

        $applicationConfig = Config::getInstance();
        $databaseSettings  = $applicationConfig->get('b8.database', []);

        $phinxSettings = [];
        if ($databaseSettings) {
            $phinxSettings = [
                'paths' => [
                    'migrations' => ROOT_DIR . 'src/PHPCensor/Migrations',
                ],
                'environments' => [
                    'default_migration_table' => 'migration',
                    'default_database' => 'php-censor',
                    'php-censor' => [
                        'adapter' => $databaseSettings['type'],
                        'host' => $databaseSettings['servers']['write'][0]['host'],
                        'name' => $databaseSettings['name'],
                        'user' => $databaseSettings['username'],
                        'pass' => $databaseSettings['password'],
                    ],
                ],
            ];
        }

        if (!empty($databaseSettings['port'])) {
            $phinxSettings['environments']['php-censor']['port'] = (integer)$databaseSettings['port'];
        }

        $phinxConfig = new PhinxConfig($phinxSettings);

        $this->add(
            (new Create())
                ->setConfig($phinxConfig)
                ->setName('php-censor-migrations:create')
        );
        $this->add(
            (new Migrate())
                ->setConfig($phinxConfig)
                ->setName('php-censor-migrations:migrate')
        );
        $this->add(
            (new Rollback())
                ->setConfig($phinxConfig)
                ->setName('php-censor-migrations:rollback')
        );
        $this->add(
            (new Status())
                ->setConfig($phinxConfig)
                ->setName('php-censor-migrations:status')
        );

        /** @var UserStore $userStore */
        $userStore = Factory::getStore('User');

        /** @var ProjectStore $projectStore */
        $projectStore = Factory::getStore('Project');

        /** @var BuildStore $buildStore */
        $buildStore = Factory::getStore('Build');

        $logger = $this->initLogger($applicationConfig);

        $this->add(new RunCommand($logger));
        $this->add(new RebuildCommand($logger));
        $this->add(new InstallCommand());
        $this->add(new CreateAdminCommand($userStore));
        $this->add(new CreateBuildCommand($projectStore, new BuildService($buildStore)));
        $this->add(new WorkerCommand($logger));
        $this->add(new RebuildQueueCommand($logger));
        $this->add(new ScheduleBuildCommand($projectStore, $buildStore, new BuildService($buildStore)));
        $this->add(new BuildNotificationCommand($logger));
    }

    public function getHelp()
    {
        return self::LOGO . parent::getHelp();
    }

    /**
     * Returns the long version of the application.
     *
     * @return string The long application version
     */
    public function getLongVersion()
    {
        if ('UNKNOWN' !== $this->getName()) {
            if ('UNKNOWN' !== $this->getVersion()) {
                return sprintf('<info>%s</info> v%s', $this->getName(), $this->getVersion());
            }

            return sprintf('<info>%s</info>', $this->getName());
        }

        return '<info>Console Tool</info>';
    }
}
