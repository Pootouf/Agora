<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Game\Splendor\SplendorParameters;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class VersionSPLENDOR_DATA extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Insertion of tokens
        for($i = 1; $i <= 7; $i++) {
            $this->connection->insert('token_spl', ['id' => $i, 'color' => SplendorParameters::$COLOR_WHITE, 'type' => 'diamond']);
        }
        for($i = 8; $i <= 14; $i++) {
            $this->connection->insert('token_spl', ['id' => $i, 'color' => SplendorParameters::$COLOR_GREEN, 'type' => 'emerald']);
        }
        for($i = 15; $i <= 21; $i++) {
            $this->connection->insert('token_spl', ['id' => $i, 'color' => SplendorParameters::$COLOR_BLACK, 'type' => 'onyx']);
        }
        for($i = 22; $i <= 28; $i++) {
            $this->connection->insert('token_spl', ['id' => $i, 'color' => SplendorParameters::$COLOR_RED, 'type' => 'ruby']);
        }
        for($i = 29; $i <= 35; $i++) {
            $this->connection->insert('token_spl', ['id' => $i, 'color' => SplendorParameters::$COLOR_BLUE, 'type' => 'sapphire']);
        }
        for($i = 36; $i <= 40; $i++) {
            $this->connection->insert('token_spl', ['id' => $i, 'color' => SplendorParameters::$COLOR_YELLOW, 'type' => 'gold']);
        }

        // Insertion of card costs
        $priceInd = 1;
        for($i = 1; $i <= 7; $i++) {
            $this->connection->insert('card_cost_spl', [
                'id' => $i, 'color' => SplendorParameters::$COLOR_WHITE, 'price' => $priceInd
            ]);
            $priceInd++;
        }
        $priceInd = 1;
        for($i = 8; $i <= 14; $i++) {
            $this->connection->insert('card_cost_spl', [
                'id' => $i, 'color' => SplendorParameters::$COLOR_BLUE, 'price' => $priceInd
            ]);
            $priceInd++;
        }
        $priceInd = 1;
        for($i = 15; $i <= 21; $i++) {
            $this->connection->insert('card_cost_spl', [
                'id' => $i, 'color' => SplendorParameters::$COLOR_RED, 'price' => $priceInd
            ]);
            $priceInd++;
        }
        $priceInd = 1;
        for($i = 22; $i <= 28; $i++) {
            $this->connection->insert('card_cost_spl', [
                'id' => $i, 'color' => SplendorParameters::$COLOR_BLACK, 'price' => $priceInd
            ]);
            $priceInd++;
        }
        $priceInd = 1;
        for($i = 29; $i <= 35; $i++) {
            $this->connection->insert('card_cost_spl', [
                'id' => $i, 'color' => SplendorParameters::$COLOR_GREEN, 'price' => $priceInd
            ]);
            $priceInd++;
        }

        //Insertion of card 1
        $this->connection->insert('development_cards_spl', [
            'id' => 1, 'points' => 0, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 1, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 1, 'card_cost_spl_id' => 8
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 1, 'card_cost_spl_id' => 15
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 1, 'card_cost_spl_id' => 22
        ]);

        //Insertion of card 2
        $this->connection->insert('development_cards_spl', [
            'id' => 2, 'points' => 0, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 2, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 2, 'card_cost_spl_id' => 8
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 2, 'card_cost_spl_id' => 15
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 2, 'card_cost_spl_id' => 23
        ]);

        //Insertion of card 3
        $this->connection->insert('development_cards_spl', [
            'id' => 3, 'points' => 0, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 3, 'card_cost_spl_id' => 8
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 3, 'card_cost_spl_id' => 16
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 3, 'card_cost_spl_id' => 23
        ]);

        //Insertion of card 4
        $this->connection->insert('development_cards_spl', [
            'id' => 4, 'points' => 0, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 4, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 4, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 4, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 5
        $this->connection->insert('development_cards_spl', [
            'id' => 5, 'points' => 0, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 5, 'card_cost_spl_id' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 5, 'card_cost_spl_id' => 8
        ]);

        //Insertion of card 6
        $this->connection->insert('development_cards_spl', [
            'id' => 6, 'points' => 0, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 6, 'card_cost_spl_id' => 9
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 6, 'card_cost_spl_id' => 16
        ]);

        //Insertion of card 7
        $this->connection->insert('development_cards_spl', [
            'id' => 7, 'points' => 0, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 7, 'card_cost_spl_id' => 17
        ]);

        //Insertion of card 8
        $this->connection->insert('development_cards_spl', [
            'id' => 8, 'points' => 1, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 8, 'card_cost_spl_id' => 25
        ]);

        //Insertion of card 9
        $this->connection->insert('development_cards_spl', [
            'id' => 9, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 9, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 9, 'card_cost_spl_id' => 15
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 9, 'card_cost_spl_id' => 22
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 9, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 10
        $this->connection->insert('development_cards_spl', [
            'id' => 10, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 10, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 10, 'card_cost_spl_id' => 16
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 10, 'card_cost_spl_id' => 22
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 10, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 11
        $this->connection->insert('development_cards_spl', [
            'id' => 11, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 11, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 11, 'card_cost_spl_id' => 16
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 11, 'card_cost_spl_id' => 30
        ]);

        //Insertion of card 12
        $this->connection->insert('development_cards_spl', [
            'id' => 12, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 12, 'card_cost_spl_id' => 8
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 12, 'card_cost_spl_id' => 15
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 12, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 13
        $this->connection->insert('development_cards_spl', [
            'id' => 13, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 13, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 13, 'card_cost_spl_id' => 23
        ]);

        //Insertion of card 14
        $this->connection->insert('development_cards_spl', [
            'id' => 14, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 14, 'card_cost_spl_id' => 23
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 14, 'card_cost_spl_id' => 30
        ]);

        //Insertion of card 15
        $this->connection->insert('development_cards_spl', [
            'id' => 15, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 15, 'card_cost_spl_id' => 24
        ]);

        //Insertion of card 16
        $this->connection->insert('development_cards_spl', [
            'id' => 16, 'points' => 1, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 16, 'card_cost_spl_id' => 18
        ]);

        //Insertion of card 17
        $this->connection->insert('development_cards_spl', [
            'id' => 17, 'points' => 0, 'color' => SplendorParameters::$COLOR_RED, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 17, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 17, 'card_cost_spl_id' => 8
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 17, 'card_cost_spl_id' => 22
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 17, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 18
        $this->connection->insert('development_cards_spl', [
            'id' => 18, 'points' => 0, 'color' => SplendorParameters::$COLOR_RED, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 18, 'card_cost_spl_id' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 18, 'card_cost_spl_id' => 8
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 18, 'card_cost_spl_id' => 22
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 18, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 19
        $this->connection->insert('development_cards_spl', [
            'id' => 19, 'points' => 0, 'color' => SplendorParameters::$COLOR_RED, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 19, 'card_cost_spl_id' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 19, 'card_cost_spl_id' => 23
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 19, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 20
        $this->connection->insert('development_cards_spl', [
            'id' => 20, 'points' => 0, 'color' => SplendorParameters::$COLOR_RED, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 20, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 20, 'card_cost_spl_id' => 15
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 20, 'card_cost_spl_id' => 24
        ]);

        //Insertion of card 21
        $this->connection->insert('development_cards_spl', [
            'id' => 21, 'points' => 0, 'color' => SplendorParameters::$COLOR_RED, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 21, 'card_cost_spl_id' => 9
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 21, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 22
        $this->connection->insert('development_cards_spl', [
            'id' => 22, 'points' => 0, 'color' => SplendorParameters::$COLOR_RED, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 22, 'card_cost_spl_id' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 22, 'card_cost_spl_id' => 16
        ]);

        //Insertion of card 23
        $this->connection->insert('development_cards_spl', [
            'id' => 23, 'points' => 0, 'color' => SplendorParameters::$COLOR_RED, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 23, 'card_cost_spl_id' => 3
        ]);

        //Insertion of card 24
        $this->connection->insert('development_cards_spl', [
            'id' => 24, 'points' => 1, 'color' => SplendorParameters::$COLOR_RED, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 24, 'card_cost_spl_id' => 4
        ]);

        //Insertion of card 25
        $this->connection->insert('development_cards_spl', [
            'id' => 25, 'points' => 0, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 25, 'card_cost_spl_id' => 8
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 25, 'card_cost_spl_id' => 15
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 25, 'card_cost_spl_id' => 22
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 25, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 26
        $this->connection->insert('development_cards_spl', [
            'id' => 26, 'points' => 0, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 26, 'card_cost_spl_id' => 8
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 26, 'card_cost_spl_id' => 15
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 26, 'card_cost_spl_id' => 22
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 26, 'card_cost_spl_id' => 30
        ]);

        //Insertion of card 27
        $this->connection->insert('development_cards_spl', [
            'id' => 27, 'points' => 0, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 27, 'card_cost_spl_id' => 9
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 27, 'card_cost_spl_id' => 22
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 27, 'card_cost_spl_id' => 30
        ]);

        //Insertion of card 28
        $this->connection->insert('development_cards_spl', [
            'id' => 28, 'points' => 0, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 28, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 28, 'card_cost_spl_id' => 8
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 28, 'card_cost_spl_id' => 22
        ]);

        //Insertion of card 29
        $this->connection->insert('development_cards_spl', [
            'id' => 29, 'points' => 0, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 29, 'card_cost_spl_id' => 16
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 29, 'card_cost_spl_id' => 22
        ]);

        //Insertion of card 30
        $this->connection->insert('development_cards_spl', [
            'id' => 30, 'points' => 0, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 30, 'card_cost_spl_id' => 9
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 30, 'card_cost_spl_id' => 23
        ]);

        //Insertion of card 31
        $this->connection->insert('development_cards_spl', [
            'id' => 31, 'points' => 0, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 31, 'card_cost_spl_id' => 10
        ]);

        //Insertion of card 32
        $this->connection->insert('development_cards_spl', [
            'id' => 32, 'points' => 1, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 32, 'card_cost_spl_id' => 32
        ]);

        //Insertion of card 33
        $this->connection->insert('development_cards_spl', [
            'id' => 33, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 33, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 33, 'card_cost_spl_id' => 8
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 33, 'card_cost_spl_id' => 15
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 33, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 34
        $this->connection->insert('development_cards_spl', [
            'id' => 34, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 34, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 34, 'card_cost_spl_id' => 9
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 34, 'card_cost_spl_id' => 15
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 34, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 35
        $this->connection->insert('development_cards_spl', [
            'id' => 35, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 35, 'card_cost_spl_id' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 35, 'card_cost_spl_id' => 9
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 35, 'card_cost_spl_id' => 15
        ]);

        //Insertion of card 36
        $this->connection->insert('development_cards_spl', [
            'id' => 36, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 36, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 36, 'card_cost_spl_id' => 22
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 36, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 37
        $this->connection->insert('development_cards_spl', [
            'id' => 37, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 37, 'card_cost_spl_id' => 15
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 37, 'card_cost_spl_id' => 30
        ]);

        //Insertion of card 38
        $this->connection->insert('development_cards_spl', [
            'id' => 38, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 38, 'card_cost_spl_id' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 38, 'card_cost_spl_id' => 30
        ]);

        //Insertion of card 39
        $this->connection->insert('development_cards_spl', [
            'id' => 39, 'points' => 0, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 39, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 40
        $this->connection->insert('development_cards_spl', [
            'id' => 40, 'points' => 1, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 40, 'card_cost_spl_id' => 11
        ]);

        //Insertion of card 41
        $this->connection->insert('development_cards_spl', [
            'id' => 41, 'points' => 1, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 41, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 41, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 41, 'card_cost_spl_id' => 30
        ]);

        //Insertion of card 42
        $this->connection->insert('development_cards_spl', [
            'id' => 42, 'points' => 1, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 42, 'card_cost_spl_id' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 42, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 42, 'card_cost_spl_id' => 23
        ]);

        //Insertion of card 43
        $this->connection->insert('development_cards_spl', [
            'id' => 43, 'points' => 2, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 43, 'card_cost_spl_id' => 4
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 43, 'card_cost_spl_id' => 9
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 43, 'card_cost_spl_id' => 22
        ]);

        //Insertion of card 44
        $this->connection->insert('development_cards_spl', [
            'id' => 44, 'points' => 2, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 44, 'card_cost_spl_id' => 12
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 44, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 45
        $this->connection->insert('development_cards_spl', [
            'id' => 45, 'points' => 2, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 45, 'card_cost_spl_id' => 33
        ]);

        //Insertion of card 46
        $this->connection->insert('development_cards_spl', [
            'id' => 46, 'points' => 3, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 46, 'card_cost_spl_id' => 34
        ]);

        //Insertion of card 47
        $this->connection->insert('development_cards_spl', [
            'id' => 47, 'points' => 1, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 47, 'card_cost_spl_id' => 9
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 47, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 47, 'card_cost_spl_id' => 30
        ]);

        //Insertion of card 48
        $this->connection->insert('development_cards_spl', [
            'id' => 48, 'points' => 1, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 48, 'card_cost_spl_id' => 9
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 48, 'card_cost_spl_id' => 24
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 48, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 49
        $this->connection->insert('development_cards_spl', [
            'id' => 49, 'points' => 2, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 49, 'card_cost_spl_id' => 5
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 49, 'card_cost_spl_id' => 10
        ]);

        //Insertion of card 50
        $this->connection->insert('development_cards_spl', [
            'id' => 50, 'points' => 2, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 50, 'card_cost_spl_id' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 50, 'card_cost_spl_id' => 15
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 50, 'card_cost_spl_id' => 25
        ]);

        //Insertion of card 51
        $this->connection->insert('development_cards_spl', [
            'id' => 51, 'points' => 2, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 51, 'card_cost_spl_id' => 12
        ]);

        //Insertion of card 52
        $this->connection->insert('development_cards_spl', [
            'id' => 52, 'points' => 3, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 52, 'card_cost_spl_id' => 13
        ]);

        //Insertion of card 53
        $this->connection->insert('development_cards_spl', [
            'id' => 53, 'points' => 1, 'color' => SplendorParameters::$COLOR_RED, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 53, 'card_cost_spl_id' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 53, 'card_cost_spl_id' => 16
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 53, 'card_cost_spl_id' => 24
        ]);

        //Insertion of card 54
        $this->connection->insert('development_cards_spl', [
            'id' => 54, 'points' => 1, 'color' => SplendorParameters::$COLOR_RED, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 54, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 54, 'card_cost_spl_id' => 16
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 54, 'card_cost_spl_id' => 24
        ]);

        //Insertion of card 55
        $this->connection->insert('development_cards_spl', [
            'id' => 55, 'points' => 2, 'color' => SplendorParameters::$COLOR_RED, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 55, 'card_cost_spl_id' => 1
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 55, 'card_cost_spl_id' => 11
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 55, 'card_cost_spl_id' => 30
        ]);

        //Insertion of card 56
        $this->connection->insert('development_cards_spl', [
            'id' => 56, 'points' => 2, 'color' => SplendorParameters::$COLOR_RED, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 56, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 56, 'card_cost_spl_id' => 26
        ]);

        //Insertion of card 57
        $this->connection->insert('development_cards_spl', [
            'id' => 57, 'points' => 2, 'color' => SplendorParameters::$COLOR_RED, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 57, 'card_cost_spl_id' => 26
        ]);

        //Insertion of card 58
        $this->connection->insert('development_cards_spl', [
            'id' => 58, 'points' => 3, 'color' => SplendorParameters::$COLOR_RED, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 58, 'card_cost_spl_id' => 20
        ]);

        //Insertion of card 59
        $this->connection->insert('development_cards_spl', [
            'id' => 59, 'points' => 1, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 59, 'card_cost_spl_id' => 16
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 59, 'card_cost_spl_id' => 23
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 59, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 60
        $this->connection->insert('development_cards_spl', [
            'id' => 60, 'points' => 1, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 60, 'card_cost_spl_id' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 60, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 60, 'card_cost_spl_id' => 17
        ]);

        //Insertion of card 61
        $this->connection->insert('development_cards_spl', [
            'id' => 61, 'points' => 2, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 61, 'card_cost_spl_id' => 18
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 61, 'card_cost_spl_id' => 23
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 61, 'card_cost_spl_id' => 29
        ]);

        //Insertion of card 62
        $this->connection->insert('development_cards_spl', [
            'id' => 62, 'points' => 2, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 62, 'card_cost_spl_id' => 19
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 62, 'card_cost_spl_id' => 24
        ]);

        //Insertion of card 63
        $this->connection->insert('development_cards_spl', [
            'id' => 63, 'points' => 2, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 63, 'card_cost_spl_id' => 19
        ]);

        //Insertion of card 64
        $this->connection->insert('development_cards_spl', [
            'id' => 64, 'points' => 3, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 64, 'card_cost_spl_id' => 6
        ]);

        //Insertion of card 65
        $this->connection->insert('development_cards_spl', [
            'id' => 65, 'points' => 1, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 65, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 65, 'card_cost_spl_id' => 9
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 65, 'card_cost_spl_id' => 30
        ]);

        //Insertion of card 66
        $this->connection->insert('development_cards_spl', [
            'id' => 66, 'points' => 1, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 66, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 66, 'card_cost_spl_id' => 23
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 66, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 67
        $this->connection->insert('development_cards_spl', [
            'id' => 67, 'points' => 2, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 67, 'card_cost_spl_id' => 8
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 67, 'card_cost_spl_id' => 16
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 67, 'card_cost_spl_id' => 32
        ]);

        //Insertion of card 68
        $this->connection->insert('development_cards_spl', [
            'id' => 68, 'points' => 2, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 68, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 68, 'card_cost_spl_id' => 33
        ]);

        //Insertion of card 69
        $this->connection->insert('development_cards_spl', [
            'id' => 69, 'points' => 2, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 69, 'card_cost_spl_id' => 5
        ]);

        //Insertion of card 70
        $this->connection->insert('development_cards_spl', [
            'id' => 70, 'points' => 3, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 2
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 70, 'card_cost_spl_id' => 27
        ]);

        //Insertion of card 71
        $this->connection->insert('development_cards_spl', [
            'id' => 71, 'points' => 3, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 71, 'card_cost_spl_id' => 5
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 71, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 71, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 71, 'card_cost_spl_id' => 24
        ]);

        //Insertion of card 72
        $this->connection->insert('development_cards_spl', [
            'id' => 72, 'points' => 4, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 72, 'card_cost_spl_id' => 14
        ]);

        //Insertion of card 73
        $this->connection->insert('development_cards_spl', [
            'id' => 73, 'points' => 4, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 73, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 73, 'card_cost_spl_id' => 13
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 73, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 74
        $this->connection->insert('development_cards_spl', [
            'id' => 74, 'points' => 5, 'color' => SplendorParameters::$COLOR_GREEN, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 74, 'card_cost_spl_id' => 14
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 74, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 75
        $this->connection->insert('development_cards_spl', [
            'id' => 75, 'points' => 3, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 75, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 75, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 75, 'card_cost_spl_id' => 26
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 75, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 76
        $this->connection->insert('development_cards_spl', [
            'id' => 76, 'points' => 4, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 76, 'card_cost_spl_id' => 7
        ]);

        //Insertion of card 77
        $this->connection->insert('development_cards_spl', [
            'id' => 77, 'points' => 4, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 77, 'card_cost_spl_id' => 6
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 77, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 77, 'card_cost_spl_id' => 24
        ]);

        //Insertion of card 78
        $this->connection->insert('development_cards_spl', [
            'id' => 78, 'points' => 5, 'color' => SplendorParameters::$COLOR_BLUE, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 78, 'card_cost_spl_id' => 7
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 78, 'card_cost_spl_id' => 10
        ]);

        //Insertion of card 79
        $this->connection->insert('development_cards_spl', [
            'id' => 79, 'points' => 3, 'color' => SplendorParameters::$COLOR_RED, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 79, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 79, 'card_cost_spl_id' => 12
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 79, 'card_cost_spl_id' => 24
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 79, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 80
        $this->connection->insert('development_cards_spl', [
            'id' => 80, 'points' => 4, 'color' => SplendorParameters::$COLOR_RED, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 80, 'card_cost_spl_id' => 35
        ]);

        //Insertion of card 81
        $this->connection->insert('development_cards_spl', [
            'id' => 81, 'points' => 4, 'color' => SplendorParameters::$COLOR_RED, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 81, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 81, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 81, 'card_cost_spl_id' => 34
        ]);

        //Insertion of card 82
        $this->connection->insert('development_cards_spl', [
            'id' => 82, 'points' => 5, 'color' => SplendorParameters::$COLOR_RED, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 82, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 82, 'card_cost_spl_id' => 35
        ]);

        //Insertion of card 83
        $this->connection->insert('development_cards_spl', [
            'id' => 83, 'points' => 3, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 83, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 83, 'card_cost_spl_id' => 19
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 83, 'card_cost_spl_id' => 24
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 83, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 84
        $this->connection->insert('development_cards_spl', [
            'id' => 84, 'points' => 4, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 84, 'card_cost_spl_id' => 28
        ]);

        //Insertion of card 85
        $this->connection->insert('development_cards_spl', [
            'id' => 85, 'points' => 4, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 85, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 85, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 85, 'card_cost_spl_id' => 27
        ]);

        //Insertion of card 86
        $this->connection->insert('development_cards_spl', [
            'id' => 86, 'points' => 5, 'color' => SplendorParameters::$COLOR_WHITE, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 86, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 86, 'card_cost_spl_id' => 28
        ]);

        //Insertion of card 87
        $this->connection->insert('development_cards_spl', [
            'id' => 87, 'points' => 3, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 87, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 87, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 87, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 87, 'card_cost_spl_id' => 33
        ]);

        //Insertion of card 88
        $this->connection->insert('development_cards_spl', [
            'id' => 88, 'points' => 4, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 88, 'card_cost_spl_id' => 21
        ]);

        //Insertion of card 89
        $this->connection->insert('development_cards_spl', [
            'id' => 89, 'points' => 4, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 89, 'card_cost_spl_id' => 20
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 89, 'card_cost_spl_id' => 24
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 89, 'card_cost_spl_id' => 31
        ]);

        //Insertion of card 90
        $this->connection->insert('development_cards_spl', [
            'id' => 90, 'points' => 5, 'color' => SplendorParameters::$COLOR_BLACK, 'level' => 3
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 90, 'card_cost_spl_id' => 21
        ]);
        $this->connection->insert('development_cards_spl_card_cost_spl', [
            'development_cards_spl_id' => 90, 'card_cost_spl_id' => 24
        ]);

        //Insertion of noble tiles

        //Insertion of noble tile 1
        $this->connection->insert('noble_tile_spl', ['id' => 1, 'prestige_points' => 3]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 1, 'card_cost_spl_id' => 18
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 1, 'card_cost_spl_id' => 32
        ]);

        //Insertion of noble tile 2
        $this->connection->insert('noble_tile_spl', ['id' => 2, 'prestige_points' => 3]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 2, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 2, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 2, 'card_cost_spl_id' => 24
        ]);

        //Insertion of noble tile 3
        $this->connection->insert('noble_tile_spl', ['id' => 3, 'prestige_points' => 3]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 3, 'card_cost_spl_id' => 4
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 3, 'card_cost_spl_id' => 11
        ]);

        //Insertion of noble tile 4
        $this->connection->insert('noble_tile_spl', ['id' => 4, 'prestige_points' => 3]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 4, 'card_cost_spl_id' => 4
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 4, 'card_cost_spl_id' => 25
        ]);

        //Insertion of noble tile 5
        $this->connection->insert('noble_tile_spl', ['id' => 5, 'prestige_points' => 3]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 5, 'card_cost_spl_id' => 11
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 5, 'card_cost_spl_id' => 32
        ]);

        //Insertion of noble tile 6
        $this->connection->insert('noble_tile_spl', ['id' => 6, 'prestige_points' => 3]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 6, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 6, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 6, 'card_cost_spl_id' => 31
        ]);

        //Insertion of noble tile 7
        $this->connection->insert('noble_tile_spl', ['id' => 7, 'prestige_points' => 3]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 7, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 7, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 7, 'card_cost_spl_id' => 31
        ]);

        //Insertion of noble tile 8
        $this->connection->insert('noble_tile_spl', ['id' => 8, 'prestige_points' => 3]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 8, 'card_cost_spl_id' => 18
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 8, 'card_cost_spl_id' => 25
        ]);

        //Insertion of noble tile 9
        $this->connection->insert('noble_tile_spl', ['id' => 9, 'prestige_points' => 3]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 9, 'card_cost_spl_id' => 3
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 9, 'card_cost_spl_id' => 10
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 9, 'card_cost_spl_id' => 24
        ]);

        //Insertion of noble tile 10
        $this->connection->insert('noble_tile_spl', ['id' => 10, 'prestige_points' => 3]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 10, 'card_cost_spl_id' => 17
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 10, 'card_cost_spl_id' => 24
        ]);
        $this->connection->insert('noble_tile_spl_card_cost_spl', [
            'noble_tile_spl_id' => 10, 'card_cost_spl_id' => 31
        ]);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
