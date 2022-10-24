function initializePosition() {
    var board = new Object();
    board["0a"] = { x: 485, y: 105, deg: 0 };
    board["0b"] = { x: 505, y: 130, deg: 0 };
    board["0c"] = { x: 505, y: 147, deg: 0 };
    board["0d"] = { x: 505, y: 167, deg: 0 };
    board["0e"] = { x: 505, y: 189, deg: 0 };
    board["0f"] = { x: 505, y: 208, deg: 0 };
    board["0g"] = { x: 505, y: 230, deg: 0 };


    board["1a"] = { x: 535, y: 105, deg: 0 };

    board["2a"] = { x: 585, y: 105, deg: 0 };
    board["2b"] = { x: 565, y: 130, deg: 0 };
    board["2c"] = { x: 565, y: 145, deg: 0 };
    board["2d"] = { x: 565, y: 165, deg: 0 };
    board["2e"] = { x: 565, y: 190, deg: 0 };
    board["2f"] = { x: 565, y: 210, deg: 0 };
    board["2g"] = { x: 565, y: 230, deg: 0 };

    board["3a"] = { x: 645, y: 125, deg: 0 };
    board["3b"] = { x: 645, y: 145, deg: 0 };
    board["3c"] = { x: 645, y: 170, deg: 0 };
    board["3d"] = { x: 645, y: 190, deg: 0 };
    board["3e"] = { x: 645, y: 210, deg: 0 };

    board["4a"] = { x: 735, y: 170, deg: 30 };
    board["4b"] = { x: 725, y: 190, deg: 30 };
    board["4c"] = { x: 720, y: 215, deg: 30 };
    board["4d"] = { x: 710, y: 230, deg: 30 };

    board["5a"] = { x: 800, y: 240, deg: 45 };
    board["5b"] = { x: 780, y: 260, deg: 45 };
    board["5c"] = { x: 765, y: 275, deg: 45 };

    board["6a"] = { x: 850, y: 310, deg: 60 };
    board["6b"] = { x: 820, y: 335, deg: 60 };

    board["7a"] = { x: 870, y: 420, deg: 90 };

    board["8a"] = { x: 860, y: 535, deg: 120 };
    board["8b"] = { x: 835, y: 510, deg: 120 };

    board["9a"] = { x: 805, y: 585, deg: 150 };
    board["10b"] = { x: 775, y: 560, deg: 160 };

    board["10a"] = { x: 755, y: 595, deg: 180 };

    board["11a"] = { x: 695, y: 585, deg: 200 };
    board["11b"] = { x: 700, y: 550, deg: 200 };

    board["12a"] = { x: 625, y: 540, deg: 220 };
    board["12b"] = { x: 635, y: 505, deg: 220 };

    board["13a"] = { x: 535, y: 520, deg: 170 };
    board["14a"] = { x: 485, y: 525, deg: 175 };
    board["15a"] = { x: 435, y: 520, deg: 190 };

    board["15b"] = { x: 500, y: 485, deg: 170 };

    board["16a"] = { x: 390, y: 505, deg: 200 };
    board["16b"] = { x: 400, y: 470, deg: 200 };

    board["17a"] = { x: 340, y: 465, deg: 205 };

    board["18a"] = { x: 225, y: 360, deg: 270 };
    board["18b"] = { x: 265, y: 360, deg: 270 };

    board["19a"] = { x: 315, y: 260, deg: 350 };
    board["20a"] = { x: 400, y: 290, deg: 45 };
    board["21a"] = { x: 420, y: 360, deg: 100 };

    board["21b"] = { x: 365, y: 320, deg: 50 };

    board["22a"] = { x: 400, y: 415, deg: 120 };
    board["22b"] = { x: 365, y: 400, deg: 120 };

    board["23a"] = { x: 305, y: 525, deg: 150 };
    board["24a"] = { x: 220, y: 535, deg: 200 };

    board["24b"] = { x: 250, y: 500, deg: 180 };

    board["25a"] = { x: 140, y: 460, deg: 220 };

    board["26a"] = { x: 80, y: 330, deg: 270 };
    board["26b"] = { x: 120, y: 330, deg: 275 };

    board["27a"] = { x: 140, y: 200, deg: 310 };
    board["27b"] = { x: 170, y: 230, deg: 310 };

    board["28a"] = { x: 235, y: 145, deg: 350 };
    board["28b"] = { x: 235, y: 170, deg: 355 };
    board["28c"] = { x: 250, y: 195, deg: 355 };

    board["29a"] = { x: 320, y: 120, deg: 355 };
    board["29b"] = { x: 320, y: 145, deg: 0 };
    board["29c"] = { x: 320, y: 170, deg: 0 };
    board["29d"] = { x: 320, y: 185, deg: 0 };
    board["29e"] = { x: 320, y: 215, deg: 5 };

    board["30a"] = { x: 385, y: 105, deg: 0 };

    board["31a"] = { x: 445, y: 105, deg: 0 };
    return board;
}

function getTransition() {

    var board = new Object();

    board["0a"] = ["1a"];

    board["0b"] = ["2b", "2c"];
    board["0c"] = ["2b", "2c", "2d"];
    board["0d"] = ["2c", "2d", "2e"];
    board["0e"] = ["2d", "2e", "2f"];
    board["0f"] = ["2e", "2f", "2g"];
    board["0g"] = ["2f", "2g"];

    board["1a"] = ["2a"];

    board["2a"] = ["3a"];
    board["2b"] = ["3a", "3b"];
    board["2c"] = ["3a", "3b", "3c"];
    board["2d"] = ["3b", "3c", "3d"];
    board["2e"] = ["3c", "3d", "3e"];
    board["2f"] = ["3d", "3e"];
    board["2g"] = ["3e"];

    board["3a"] = ["4a"];
    board["3b"] = ["4a", "4b"];
    board["3c"] = ["4a", "4b", "4c"];
    board["3d"] = ["4b", "4c", "4d"];
    board["3e"] = ["4c", "4d"];

    board["4a"] = ["5a"];
    board["4b"] = ["5a", "5b"];
    board["4c"] = ["5a", "5b", "5c"];
    board["4d"] = ["5b", "5c"];

    board["5a"] = ["6a"];
    board["5b"] = ["6b"];
    board["5c"] = ["6b"];

    board["6a"] = ["7a"];
    board["6b"] = ["7a"];

    board["7a"] = ["8a", "8b"];

    board["8a"] = ["9a", "10b"];
    board["8b"] = ["9a", "10b"];

    board["9a"] = ["10a"];
    board["10a"] = ["11a", "11b"];
    board["10b"] = ["11a", "11b"];

    board["11a"] = ["12a", "12b"];
    board["11b"] = ["12a", "12b"];

    board["12a"] = ["13a", "15b"];
    board["12b"] = ["13a", "15b"];

    board["13a"] = ["14a"];
    board["14a"] = ["15a"];

    board["15a"] = ["16a"];
    board["15b"] = ["16b"];

    board["16a"] = ["17a"];
    board["16b"] = ["17a"];

    board["17a"] = ["18a", "18b"];

    board["18a"] = ["19a", "21b"];
    board["18b"] = ["19a", "21b"];

    board["19a"] = ["20a"];
    board["21b"] = ["22a", "22b"];

    board["20a"] = ["21a"];

    board["21a"] = ["22a", "22b"];

    board["22a"] = ["23a", "24b"];
    board["22b"] = ["23a", "24b"];

    board["23a"] = ["24a"];
    board["24b"] = ["25a"];

    board["24a"] = ["25a"];

    board["25a"] = ["26a", "26b"];

    board["26a"] = ["27a"];
    board["26b"] = ["27b"];

    board["27a"] = ["28a", "28b", "28c"];
    board["27b"] = ["28b", "28c"];

    board["28a"] = ["29a", "29b", "29c"];
    board["28b"] = ["29b", "29c", "29d"];
    board["28c"] = ["29c", "29d", "29e"];

    board["29a"] = ["30a", "0b", "0c"];
    board["29b"] = ["0b", "0c", "0d"];
    board["29c"] = ["0c", "0d", "0e"];
    board["29d"] = ["0d", "0e", "0f"];
    board["29e"] = ["0e", "0f", "0g"];

    board["30a"] = ["31a"];

    board["31a"] = ["0a"];

    return board;
}

