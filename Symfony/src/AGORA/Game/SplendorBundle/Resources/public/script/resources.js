var colours = ['white', 'blue', 'green', 'red', 'black'];

var all_colours = ['white', 'blue', 'green', 'red', 'black', 'gold'];

var colour_indices = {};

for (let i = 0; i < colours.length; i++) {
    colour = colours[i];
    colour_indices[colour] = i;
}
colour_indices['none'] = 5;

class Player {
    constructor(number) {
        this.number = number;
        this.cards_in_hand = [];
        this.cards_played = [];
        this.nobles = [];

        this.gems = {white: 0,
            blue: 0,
            green: 0,
            red: 0,
            black: 0,
            gold: 0};

        this.card_colours = {white: 0,
            blue: 0,
            green: 0,
            red: 0,
            black: 0};

        this.score = 0;

    }

    /*returns a copy of the player*/
    copy() {
        let copy = new Player();
        for (let colour of all_colours) {
            copy.gems[colour] = this.gems[colour];
        }
        for (let colour of colours) {
            copy.card_colours[colour] = this.card_colours[colour];
        }

        copy.nobles = this.nobles.slice();
        copy.cards_in_hand = this.cards_in_hand.slice();
        copy.cards_played = this.cards_played.slice();

        copy.score = this.score;
        copy.number = this.number;

        return copy;
    }

    /*returns the number of gems of type 'colour' the player possesses*/
    num_gems(colour) {
        return this.gems[colour];
    }

    /*returns total number of gems the player possesses*/
    total_num_gems() {
        let total = 0;
        for (let colour of all_colours) {
            total += this.gems[colour];
        }
        return total;
    }

    /*adds the given set of gems to the player's set */
    add_gems(gems) {
        for (let colour of colours) {
            if (colour in gems) {
                this.gems[colour] += gems[colour];
            }
        }
    }

    can_afford(card) {

        var missing_colours = [];
        for (var colour of colours) {
            missing_colours.push(
                Math.max(card.gems[colour] -
                    this.gems[colour] -
                    this.card_colours[colour],
                    0));
        }

        if (sum(missing_colours) > this.gems['gold']) {
            return [false,
                sum(missing_colours) - this.gems['gold']];

        }

        var cost = {};
        for (let colour of colours) {
            cost[colour] = Math.max(
                Math.min(this.gems[colour],
                    card.gems[colour] - this.card_colours[colour]),
                0);
        }
        cost['gold'] = sum(missing_colours);

        return [true, cost];
    }

    num_no_points_buys() {
        let total = 0;
        for (let card of this.cards_played) {
            if (card.points === 0) {
                total += 1;
            }
        }
        return total;
    }

    num_points_buys() {
        let total = 0;
        for (let card of this.cards_played) {
            if (card.points > 0) {
                total += 1;
            }
        }
        return total;
    }
}

class Noble {
    constructor(cards, points=3) {
        this.points = points;
        this.cards = cards;
    }
}

var nobles = [
    new Noble({red: 4, green: 4}),
    new Noble({black: 4, red: 4}),
    new Noble({blue: 4, green: 4}),
    new Noble({black: 4, white: 4}),
    new Noble({blue: 4, white: 4}),
    new Noble({black: 3, red: 3, white: 3}),
    new Noble({green: 3, blue: 3, white: 3}),
    new Noble({black: 3, red: 3, green: 3}),
    new Noble({green: 3, blue: 3, red: 3}),
    new Noble({black: 3, blue: 3, white: 3}),
];

class Card {
    constructor(tier, colour, points,
                gems) {
        // {white:0, blue:0, green:0, red:0, black:0}) {
        this.tier = tier;
        this.colour = colour;
        this.points = points;

        this.gems = gems;
        for (var colour of colours) {
            if (!gems.hasOwnProperty(colour)) {
                gems[colour] = 0;
            }
        }
    }
}

var tier_1 = [
    new Card(1, 'blue', 0, {black:3}),
    new Card(1, 'blue', 0, {white:1, black:2}),
    new Card(1, 'blue', 0, {green:2, black:2}),
    new Card(1, 'blue', 0, {white:1, green:2, red:2}),
    new Card(1, 'blue', 0, {blue:1, green:3, red:1}),
    new Card(1, 'blue', 0, {white:1, green:1, red:1, black:1}),
    new Card(1, 'blue', 0, {white:1, green:1, red:2, black:1}),
    new Card(1, 'blue', 1, {red:4}),

    new Card(1, 'red', 0, {white:3}),
    new Card(1, 'red', 0, {blue:2, green:1}),
    new Card(1, 'red', 0, {white:2, red:2}),
    new Card(1, 'red', 0, {white:2, green:1, black:2}),
    new Card(1, 'red', 0, {white:1, red:1, black:3}),
    new Card(1, 'red', 0, {white:1, blue:1, green:1, black:1}),
    new Card(1, 'red', 0, {white:2, blue:1, green:1, black:1}),
    new Card(1, 'red', 1, {white:4}),

    new Card(1, 'black', 0, {green:3}),
    new Card(1, 'black', 0, {green:2, red:1}),
    new Card(1, 'black', 0, {white:2, green:2}),
    new Card(1, 'black', 0, {white:2, blue:2, red:1}),
    new Card(1, 'black', 0, {green:1, red:3, black:1}),
    new Card(1, 'black', 0, {white:1, blue:1, green:1, red:1}),
    new Card(1, 'black', 0, {white:1, blue:2, green:1, red:1}),
    new Card(1, 'black', 1, {blue:4}),

    new Card(1, 'white', 0, {blue:3}),
    new Card(1, 'white', 0, {red:2, black:1}),
    new Card(1, 'white', 0, {blue:2, black:2}),
    new Card(1, 'white', 0, {blue:2, green:2, black:1}),
    new Card(1, 'white', 0, {white:3, blue:1, black:1}),
    new Card(1, 'white', 0, {blue:1, green:1, red:1, black:1}),
    new Card(1, 'white', 0, {blue:1, green:2, red:1, black:1}),
    new Card(1, 'white', 1, {green:4}),

    new Card(1, 'green', 0, {red:3}),
    new Card(1, 'green', 0, {white:2, blue:1}),
    new Card(1, 'green', 0, {blue:2, red:2}),
    new Card(1, 'green', 0, {blue:1, red:2, black:2}),
    new Card(1, 'green', 0, {white:1, blue:3, green:1}),
    new Card(1, 'green', 0, {white:1, blue:1, red:1, black:1}),
    new Card(1, 'green', 0, {white:1, blue:1, red:1, black:2}),
    new Card(1, 'green', 1, {black:4})
];

var tier_2 = [
    new Card(2, 'blue', 1, {blue:2, green:2, red:3}),
    new Card(2, 'blue', 1, {blue:2, green:3, black:3}),
    new Card(2, 'blue', 2, {blue:5}),
    new Card(2, 'blue', 2, {white:5, blue:3}),
    new Card(2, 'blue', 2, {white:2, red:1, black:4}),
    new Card(2, 'blue', 3, {blue:6}),

    new Card(2, 'red', 1, {white:2, red:2, black:3}),
    new Card(2, 'red', 1, {blue:3, red:2, black:3}),
    new Card(2, 'red', 2, {black:5}),
    new Card(2, 'red', 2, {white:3, black:5}),
    new Card(2, 'red', 2, {white:1, blue:4, green:2}),
    new Card(2, 'red', 3, {red:6}),

    new Card(2, 'black', 1, {white:3, blue:2, green:2}),
    new Card(2, 'black', 1, {white:3, green:3, black:2}),
    new Card(2, 'black', 2, {white:5}),
    new Card(2, 'black', 2, {green:5, red:3}),
    new Card(2, 'black', 2, {blue:1, green:4, red:2}),
    new Card(2, 'black', 3, {black:6}),

    new Card(2, 'white', 1, {green:3, red:2, black:2}),
    new Card(2, 'white', 1, {white:2, blue:3, red:3}),
    new Card(2, 'white', 2, {red:5}),
    new Card(2, 'white', 2, {red:5, black:3}),
    new Card(2, 'white', 2, {green:1, red:4, black:2}),
    new Card(2, 'white', 3, {white:6}),

    new Card(2, 'green', 1, {white:2, blue:3, black:2}),
    new Card(2, 'green', 1, {white:3, green:2, red:3}),
    new Card(2, 'green', 2, {green:5}),
    new Card(2, 'green', 2, {blue:5, green:3}),
    new Card(2, 'green', 2, {white:4, blue:2, black:1}),
    new Card(2, 'green', 3, {green:6})
];

var tier_3 = [
    new Card(3, 'blue', 3, {white:3, green:3, red:3, black:5}),
    new Card(3, 'blue', 4, {white:7}),
    new Card(3, 'blue', 4, {white:6, blue:3, black:3}),
    new Card(3, 'blue', 5, {white:7, blue:3}),

    new Card(3, 'red', 3, {white:3, blue:5, green:3, black:5}),
    new Card(3, 'red', 4, {green:7}),
    new Card(3, 'red', 4, {blue:3, green:6, red:3}),
    new Card(3, 'red', 5, {green:7, red:3}),

    new Card(3, 'black', 3, {white:3, blue:3, green:5, red:3}),
    new Card(3, 'black', 4, {red:7}),
    new Card(3, 'black', 4, {green:3, red:6, black:3}),
    new Card(3, 'black', 5, {red:7, black:3}),

    new Card(3, 'white', 3, {blue:3, green:3, red:5, black:3}),
    new Card(3, 'white', 4, {black:7}),
    new Card(3, 'white', 4, {white:3, red:3, black:6}),
    new Card(3, 'white', 5, {white:3, black:7}),

    new Card(3, 'green', 3, {white:5, blue:3, red:3, black:3}),
    new Card(3, 'green', 4, {blue:7}),
    new Card(3, 'green', 4, {white:3, blue:6, green:3}),
    new Card(3, 'green', 5, {blue:7, green:3})
];
