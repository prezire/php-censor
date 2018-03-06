<?php use Phinx\Migration\AbstractMigration;
final class AddBuildNotificationsTable extends AbstractMigration
{
    /**
     * Allows the UI to track the user's activities whether or
     * not the build push notifications have been read.
     */
    CONST TABLE_NAME = 'build_notifications';
    public function up()
    {
        $this->createTable();
        $this->createConstraints();
    }
    public function down()
    {
        $this->removeConstraints();
        $this->table(self::TABLE_NAME)->drop();
    }
    private function removeConstraints()
    {
        $table = $this->table(self::TABLE_NAME);
        if ($table->hasIndex('build_id'))
            $table->removeIndex(['build_id'])->save();
    }
    private function createConstraints()
    {
        $table = $this->table(self::TABLE_NAME);
        $opts = ['delete'=> 'CASCADE', 'update' => 'CASCADE'];
        if (!$table->hasIndex(['build_id']))
            $table->addIndex(['build_id'])->save();
        //
        if (!$table->hasForeignKey('build_id'))
            $table
                ->addForeignKey('build_id', 'build', 'id', $opts)
                ->save();
    }
    private function createTable()
    {
        $table = $this->table(self::TABLE_NAME);
        if (!$this->hasTable(self::TABLE_NAME))
            $table->create();
        //
        if (!$table->hasColumn('build_id'))
            $table->addColumn('build_id', 'integer')->save();
        if (!$table->hasColumn('created_on'))
            $table->addColumn('created_on', 'datetime')->save();
        //If not null, indicates that the 
        //notification has been read by the user.
        if (!$table->hasColumn('updated_on'))
            $table
                ->addColumn('updated_on', 'datetime', ['null' => true])
                ->save();
    }
}
