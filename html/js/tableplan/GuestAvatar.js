// Guest Avatar class - используется при переносе гостя со стула на стул

function GuestAvatar(seatOut, guestID, x, y) {
    // groupX, groupY - координаты относительно цента группы стола

    // на стуле сидит гость
    var object = new Kinetic.Text({
        x: x,
        y: y,
        text: tablePlan.Guests[guestID].GuestName,
        fontSize: 12,
        fontFamily: 'Calibri',
        fill: 'black',
        align: 'center',
        draggable: true
    });

    object.SeatOut = seatOut;
    object.GuestID = guestID;

    object.setOffset({
        x: object.getWidth() / 2,
        y: object.getHeight() / 2
    });

    object.on("dragend", function (e) {
        if (dragAndDropSeatOver != null) {
            // seat guest
            dragAndDropSeatOver.SitGuest(this.GuestID); // Guest is automatically unseated from out seat

            // unselect seat
            dragAndDropSeatOver.SelectSeat(false);
            dragAndDropSeatOver = null;
        }
       
        this.remove();
        kineticLayer.draw();
        tablePlan.DraggedGuestAvatar = null;
    });

    return object;
}