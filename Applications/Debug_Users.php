<?php

class Debug_Users extends ApplicationBase
{
  function execute( )
  {
    $pdo = sPDO::getInstance( );

    $query = $pdo->prepare( 'SELECT * FROM users' );
    $query->execute( );
    echo '<table>';
    foreach ( $query->fetchAll( PDO::FETCH_ASSOC ) as $index => $row )
    {
      if ( $index === 0 )
      {
        echo '<tr>';
        foreach ( $row as $name => $value )
        {
          echo "<td>$name</td>";
        }
        echo '</tr>';
      }

      echo '<tr>';
      foreach ( $row as $name => $value )
      {
        echo "<td>$value</td>";
      }
      echo '</tr>';
    }
    echo '</table>';
    die( );
  }
}
