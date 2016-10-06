<?php namespace Sierra\Command\Sql\Pagination;

use Database\Database;

class PaginatedQueryHandler
{
    private $command;
    private $database;

    private $sql = '';
    private $sql_postfix = ' OFFSET %d LIMIT %d';

    private $result = array();

    public function __construct(
        Database $database,
        PaginatedQueryCommand $command
    )
    {
        $this->database = $database->get();
        $this->command = $command;
    }

    public function __invoke($sql)
    {
        $sql = str_replace('%', '%%', $sql);
        $sql .= $this->sql();
        $sql = $this->format_query($sql);

        $statement = $this->execute_query($sql);

        $this->set_result($statement);

        return $this;
    }

    //--- SQL Substitution & Execution ---//

    protected function format_query($sql)
    {
        $command = $this->command();

        return sprintf(
            $sql,
            $command->offset(),
            $command->limit()
        );
    }

    protected function execute_query($sql)
    {
        $database = $this->database();

        $statement = $database->prepare($sql);
        $statement->execute();

        return $statement;
    }

    //--- Setter for Result(s) ---//

    public function result()
    {
        return $this->result;
    }

    protected function set_result($statement)
    {
        $this->result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    //--- Getter for SQL ---//

    protected function sql()
    {
        return $this->sql_postfix;
    }

    //--- Getter & Setter for Command ---//

    protected function command()
    {
        return $this->command;
    }

    protected function set_command($command)
    {
        $this->command = $command;
    }

    //--- Getter & Setter for Database ---//

    protected function database()
    {
        return $this->database;
    }

    protected function set_database($database)
    {
        $this->database = $database;
    }
}
