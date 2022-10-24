function initializePosition() {
    let board = new Object();

    board["0a"] = { x: 428, y: 95, deg: 0 };
    board["0b"] = { x: 483, y: 139, deg: 0 };
    board["0c"] = { x: 483, y: 165, deg: 0 };
    board["0d"] = { x: 483, y: 186, deg: 0 };
    board["0e"] = { x: 483, y: 216, deg: 0 };
    
    board["1a"] = { x: 535, y: 105, deg: 0 };

    board["2a"] = { x: 575, y: 120, deg: 358 };
    board["2b"] = { x: 575, y: 161, deg: 352 };
    board["2c"] = { x: 574, y: 190, deg: 345 };
    
    board["3a"] = { x: 691, y: 121, deg: 8 };
    board["3b"] = { x: 682, y: 144, deg: 10 };
    board["3c"] = { x: 674, y: 168, deg: 9 };
    board["3d"] = { x: 665, y: 187, deg: 9 };
    
    board["4a"] = { x: 788, y: 168, deg: 37 };
    board["4b"] = { x: 771, y: 188, deg: 36 };
    board["4c"] = { x: 753, y: 204, deg: 36 };
    board["4d"] = { x: 740, y: 216, deg: 34 };

    board["5a"] = { x: 838, y: 258, deg: 64 };
    board["5b"] = { x: 802, y: 272, deg: 52 };
   
    board["6a"] = { x: 837, y: 369, deg: 94 };
   
    board["7a"] = { x: 806, y: 494, deg: 131 };
    board["7b"] = { x: 775, y: 464, deg: 132 };

    board["8a"] = { x: 763, y: 535, deg: 149 };
    
    board["9a"] = { x: 700, y: 555, deg: 169 };
    board["9b"] = { x: 692, y: 513, deg: 170 };

    board["10b"] = { x: 551, y: 531, deg: 187 };
    board["10a"] = { x: 540, y: 557, deg: 193 };

    board["11a"] = { x: 490, y: 514, deg: 211 };
    
    board["12a"] = { x: 393, y: 514, deg: 179 };
    board["12c"] = { x: 471, y: 455, deg: 288 };
    board["12d"] = { x: 404, y: 560, deg: 179 };

    board["13a"] = { x: 300, y: 511, deg: 179 };
    board["13b"] = { x: 521, y: 453, deg: 315 };
    board["13c"] = { x: 513, y: 404, deg: 335 };
    board["13d"] = { x: 301, y: 558, deg: 179 };

    board["14a"] = { x: 201, y: 518, deg: 184 };
    board["14b"] = { x: 596, y: 422, deg: 352 };
    board["14d"] = { x: 192, y: 557, deg: 182 };

    board["15b"] = { x: 686, y: 442, deg: 331 };
    board["15c"] = { x: 663, y: 378, deg: 313 };
    board["15d"] = { x: 131, y: 544, deg: 215 };

    board["16a"] = { x: 145, y: 498, deg: 232 };
    board["16b"] = { x: 724, y: 345, deg: 269 };
    board["16d"] = { x: 94, y: 496, deg: 252 };

    board["17b"] = { x: 681, y: 275, deg: 216 };
    board["17c"] = { x: 664, y: 321, deg: 225 };
    board["17d"] = { x: 95, y: 437, deg: 292 };

    board["18a"] = { x: 148, y: 441, deg: 311 };
    board["18b"] = { x: 594, y: 262, deg: 165 };
    board["18c"] = { x: 596, y: 305, deg: 167 };
    board["18d"] = { x: 132, y: 392, deg: 325 };

    board["19a"] = { x: 196, y: 419, deg: 6 };
    board["19b"] = { x: 536, y: 308, deg: 170 };
    board["19d"] = { x: 191, y: 383, deg: 5 }; 

    board["20a"] = { x: 248, y: 429, deg: 4 };
    board["20b"] = { x: 464, y: 292, deg: 175 };
    board["20c"] = { x: 472, y: 336, deg: 178 };
    board["20d"] = { x: 244, y: 389, deg: 4 };

    board["21a"] = { x: 304, y: 424, deg: 340 };
    board["21b"] = { x: 398, y: 294, deg: 174 };
    board["21c"] = { x: 404, y: 335, deg: 179 };

    board["22a"] = { x: 335, y: 377, deg: 280 };
    board["22d"] = { x: 297, y: 372, deg: 300 };

    board["23a"] = { x: 325, y: 318, deg: 202 };

    board["24a"] = { x: 264, y: 317, deg: 194 };
    board["24b"] = { x: 284, y: 288, deg: 192 };

    board["25a"] = { x: 220, y: 290, deg: 203 };

    board["26a"] = { x: 156, y: 289, deg: 205 };
    board["26b"] = { x: 177, y: 261, deg: 205 };

    board["27a"] = { x: 95, y: 231, deg: 260 };
    
    board["28a"] = { x: 132, y: 147, deg: 336 };
    board["28b"] = { x: 235, y: 170, deg: 355 };

    board["29a"] = { x: 222, y: 131, deg: 3 };
    board["29b"] = { x: 224, y: 163, deg: 8 };
    board["29c"] = { x: 226, y: 190, deg: 9 };
    
    board["30a"] = { x: 297, y: 125, deg: 1 };
    board["30b"] = { x: 305, y: 174, deg: 5 };
    board["30c"] = { x: 307, y: 207, deg: 6 };

    board["31a"] = { x: 366, y: 95, deg: 0 };
    return board;
}

function getTransition() {
    let board = new Object();

    board["0a"] = ["1a"];

    board["0b"] = ["2a", "2b"];
    board["0c"] = ["2a", "2b"];
    board["0d"] = ["2b", "2c"];
    board["0e"] = ["2b", "2c"];

    board["1a"] = ["2a"];

    board["2a"] = ["3a", "3b"];
    board["2b"] = ["3a", "3b", "3c", "3d"];
    board["2c"] = ["3c", "3d"];
    
    board["3a"] = ["4a", "4b"];
    board["3b"] = ["4a", "4b", "4c"];
    board["3c"] = ["4b", "4c", "4d"];
    board["3d"] = ["4c", "4d"];

    board["4a"] = ["5a"];
    board["4b"] = ["5a"];
    board["4c"] = ["5b"];
    board["4d"] = ["5b"];

    board["5a"] = ["6a"];
    board["5b"] = ["6a"];

    board["6a"] = ["7a", "7b"];

    board["7a"] = ["8a", "9b"];
    board["7b"] = ["8a", "9b"];

    board["8a"] = ["9a"];

    board["9a"] = ["10a", "10b"];
    board["9b"] = ["10a", "10b"];

    board["10a"] = ["11a"];
    board["10b"] = ["11a"];

    board["11a"] = ["12a", "12d", "13b", "12c"];
    
    board["12a"] = ["13a", "13d"];
    board["12c"] = ["13c"];
    board["12d"] = ["13d", "13a"];

    board["13a"] = ["14a", "14d"];
    board["13d"] = ["14d", "14a"];
    board["13b"] = ["14b"];
    board["13c"] = ["14b"];

    board["14a"] = ["16a", "15d"];
    board["14b"] = ["15c", "15b"];
    board["14d"] = ["15d", "16a"];
    
    board["15b"] = ["16b"];
    board["15c"] = ["17c"];
    board["15d"] = ["16d"];

    board["16a"] = ["18a"];
    board["16b"] = ["17b"];
    board["16d"] = ["17d"];

    board["17b"] = ["18b", "18c"];
    board["17c"] = ["18c", "18b"];
    board["17d"] = ["18d"];

    board["18a"] = ["19a", "19d"];
    board["18b"] = ["19b"];
    board["18c"] = ["19b"];
    board["18d"] = ["19d", "18a"];

    board["19a"] = ["20a", "20d"];
    board["19b"] = ["20b", "20c"];
    board["19d"] = ["20d", "20a"];

    board["20a"] = ["21a", "22d"];
    board["20b"] = ["21b", "21c"];
    board["20c"] = ["21c", "21b"];
    board["20d"] = ["22d", "21a"];

    board["21a"] = ["22a"];
    board["21b"] = ["23a"];
    board["21c"] = ["23a"];

    board["22a"] = ["23a"];
    board["22d"] = ["23a"];

    board["23a"] = ["24a", "24b"];

    board["24a"] = ["25a"];
    board["24b"] = ["25a"];

    board["25a"] = ["26a", "26b"];

    board["26a"] = ["27a", "28b"];
    board["26b"] = ["28b", "27a"];

    board["27a"] = ["28a"];

    board["28a"] = ["29a", "29b"];
    board["28b"] = ["29a", "29b", "29c"];

    board["29a"] = ["30a", "30b"];
    board["29b"] = ["30a", "30b", "30c"];
    board["29c"] = ["30c", "30b"];

    board["30a"] = ["31a", "0b", "0c"];
    board["31a"] = ["0a"];

    return board;
}