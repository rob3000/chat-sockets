<?php
namespace Server;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {

    /**
     * The list of clients.
     *
     * @var \SplObjectStorage
     */
    protected $clients;


    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * When a connection is opened.
     *
     * @param ConnectionInterface $conn
     *   The connection.
     */
    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        $msg = json_encode([
            'message' => 'Welcome! This is a demo chat app and no information is saved.',
            'user' => 0,
        ]);

        // Send our welcome message to the connected client.
        $conn->send($msg);

        echo "New connection! ({$conn->resourceId})\n";
    }

    /**
     * When a message is sent.
     *
     * @param ConnectionInterface $from
     *   The connection the message was sent form.
     * @param string $msg
     *   The message.
     */
    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;

        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        // Here is where you could look to save items to a persistent storage.
        // For this example were going to store the Data in a flat storage.

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    /**
     * When the connection is closed.
     *
     * @param ConnectionInterface $conn
     *   The connection closing.
     */
    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    /**
     * When the connection has an error.
     *
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
