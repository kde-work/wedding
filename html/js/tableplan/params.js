
// resize boxes params
var _browserBorderShift = 10;
var _minimumPlanWidth = 20;
var _minimumPlanHeight = 20;

// table params
var _longTableHeight = 60;
var _seatSize = 60;
var _seatChildSize = 50;
var _seatsDelta = 4;


// RectObject default sizes
// 0 - dance floor
// 1 - DJ
// 2 - pillar
// 3 - bar
// 4 - gifts table
// 5 - cacke table
// 6 - any object

var RectObjectWidth = [7 * _seatSize, 3 * _seatSize, 1 * _seatSize, 5 * _seatSize, 2 * _seatSize, 2 * _seatSize, 3 * _seatSize];
var RectObjectHeight = [7 * _seatSize, 2 * _seatSize, 1 * _seatSize, 1 * _seatSize, 2 * _seatSize, 2 * _seatSize, 3 * _seatSize];
var RectObjectName = ['Танцпол', 'Пульт диджея', 'Колонна', 'Бар', 'Стол с подарками', 'Стол с тортом'];


// license limitations
// unregister user
var unregGuests = 0;
var unregTables = 0;

// free user
var freeGuests = 0;
var freeTables = 0;

// personal
var personalGuests = 0;
var personalTables = 0;

// professional
var profGuests = 0;
var profTables = 0;
