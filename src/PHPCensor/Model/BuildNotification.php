<?php namespace PHPCensor\Model;
use PHPCensor\Builder;
use PHPCensor\Store\BuildErrorStore;
use Symfony\Component\Yaml\Parser as YamlParser;
use PHPCensor\Model;
use b8\Store\Factory;
use DateTime;
final class BuildNotification extends Model
{
    /**
     * @var string
     */
    protected $tableName = 'build_notifications';

    /**
     * @var integer
     */
    protected $newErrorsCount = null;

    /**
     * @var array
     */
    protected $data = array(
        'id'         => null,
        'user_id'    => 0,
        'build_id'   => null,
        'created_on' => null,
        'updated_on' => null,
    );

    /**
     * @var array  Direct property getters.
     */
    protected $getters = array(
        'id'         => 'getId',
        'user_id'    => 'getUserId',
        'build_id'   => 'getBuildId',
        'created_on' => 'getCreatedOn',
        'updated_on' => 'getUpdatedOn',
        //Foreign key getters.
        'Build' => 'getBuild'
    );

    /**
     * @var array  Direct property setters.
     */
    protected $setters = [
        'id'        => 'setId',
        'user_id'   => 'setUserId',
        'build_id'  => 'setBuildId',
        'create_on' => 'setCreatedOn',
        'start_on'  => 'setUpdatedOn',
        //Foreign key setters.
        'Build' => 'setBuild'
    ];

    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->data['id'];
    }

    /**
     * @param $value int
     */
    public function setId($value)
    {
        $this->validateNotNull('id', $value);
        $this->validateInt('id', $value);
        if ($this->data['id'] === $value) {
            return;
        }
        $this->data['id'] = $value;
        $this->setModified('id');
    }

    /**
     * @return int
     */
    public function getBuildId()
    {
        return (int)$this->data['build_id'];
    }

    /**
     * @param $value int
     */
    public function setBuildId($value)
    {
        $this->validateNotNull('build_id', $value);
        $this->validateInt('build_id', $value);
        if ($this->data['build_id'] === $value) {
            return;
        }
        $this->data['build_id'] = $value;
        $this->setModified('build_id');
    }

    /**
     * @return \DateTime
     */
    public function getCreateDate()
    {
        $rtn = $this->data['create_date'];
        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }
        return $rtn;
    }

    /**
     * @param $value \DateTime
     */
    public function setCreateDate($value)
    {
        $this->validateDate('create_date', $value);
        if ($this->data['create_date'] === $value) {
            return;
        }
        $this->data['create_date'] = $value;
        $this->setModified('create_date');
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        $rtn = $this->data['start_date'];
        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }
        return $rtn;
    }

    /**
     * @param $value \DateTime
     */
    public function setStartDate($value)
    {
        $this->validateDate('start_date', $value);
        if ($this->data['start_date'] === $value) {
            return;
        }
        $this->data['start_date'] = $value;
        $this->setModified('start_date');
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->data['user_id'];
    }

    /**
     * @param $value int
     */
    public function setUserId($value)
    {
        $this->validateNotNull('user_id', $value);
        $this->validateInt('user_id', $value);
        if ($this->data['user_id'] === $value) {
            return;
        }
        $this->data['user_id'] = $value;
        $this->setModified('user_id');
    }

    /**
     * Get the Project model for this Build by Id.
     * @return \PHPCensor\Model\Project
     */
    public function getBuild()
    {
        $key = $this->getBuildId();
        if (empty($key)) {
            return null;
        }
        return Factory::getStore('Build', 'PHPCensor')->getById($key);
    }

    /**
     * Set Build - Accepts an ID, an array representing a Build or a Build model.
     * @param $value mixed
     */
    public function setBuild($value)
    {
        // Is this an instance of Build?
        if ($value instanceof Build) {
            return $this->setBuildObject($value);
        }
        // Is this an array representing a Build item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setBuildId($value['id']);
        }
        // Is this a scalar value representing the ID of this foreign key?
        return $this->setBuildId($value);
    }
}