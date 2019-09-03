var dragManager = new function () {

    /**
     * составной объект для хранения информации о переносе:
     * {
     *   elem - элемент, на котором была зажата мышь
     *   avatar - аватар
     *   downX/downY - координаты, на которых был mousedown
     *   shiftX/shiftY - относительный сдвиг курсора от угла элемента
     * }
    */
    var dragObject = {};

    var self = this;

    function onMouseDown(e) {
        e = fixEvent(e);

        if (e.which != 1) return;

        var elem = findDraggable(e);
        if (!elem) return;

        dragObject.elem = elem;

        // запомним, что элемент нажат на текущих координатах pageX/pageY
        dragObject.downX = e.pageX;
        dragObject.downY = e.pageY;

        return false;
    }

    function onMouseMove(e) {
        if ((tablePlan !== undefined) && (tablePlan != null)) {
            if (tablePlan.DraggedGuestAvatar != null) {
                // guest is dragged from seat to seat. Canvas drag
                e = fixEvent(e);
                tablePlan.onMoveAnimation(e);

                return;
            }
        }

        if (!dragObject.elem)
            return; // элемент не зажат

        e = fixEvent(e);

        if (!dragObject.avatar) { // если перенос не начат...
            var moveX = e.pageX - dragObject.downX;
            var moveY = e.pageY - dragObject.downY;

            // если мышь передвинулась в нажатом состоянии недостаточно далеко
            if (Math.abs(moveX) < 3 && Math.abs(moveY) < 3) {
                return;
            }

            // начинаем перенос
            dragObject.avatar = createAvatar(e); // создать аватар
            if (!dragObject.avatar) { // отмена переноса, нельзя "захватить" за эту часть элемента
                dragObject = {};
                return;
            }

            // аватар создан успешно
            // создать вспомогательные свойства shiftX/shiftY
            var coords = getCoords(dragObject.avatar);
            dragObject.shiftX = dragObject.downX - coords.left;
            dragObject.shiftY = dragObject.downY - coords.top;

            startDrag(e); // отобразить начало переноса
        }

        // отобразить перенос объекта при каждом движении мыши
        self.onMoveAnimation(dragObject, e);

        dragObject.avatar.style.left = e.pageX - dragObject.shiftX + 'px';
        dragObject.avatar.style.top = e.pageY - dragObject.shiftY + 'px';

        return false;
    }

    function onMouseUp(e) {
        if (dragObject.avatar) { // если перенос идет
            e = fixEvent(e);
            finishDrag(e);
        }

        // перенос либо не начинался, либо завершился
        // в любом случае очистим "состояние переноса" dragObject
        dragObject = {};
    }

    function finishDrag(e) {
        // we do need to do check for resize boxes
        if ((dragObject.elem.className == "resize_horizontal") || (dragObject.elem.className == "resize_vertical")) {
            self.onDragEnd(dragObject, dropElem);
            dragObject = {};
            return;
        }

        var dropElem = findDroppable(e);

        if (!dropElem) {
            self.onDragCancel(dragObject);
        } else {
            // put on droppable object
            if (dropElem.className == "plannerCanvas") {
                // drop on table plan. Check for seats.
                var seat = tablePlan.GetSeatUnderClientXY(e.pageX - parseInt(dropElem.style.left), e.pageY - parseInt(dropElem.style.top));
                if (seat == null) {
                    self.onDragCancel(dragObject);
                }
                else {
                    // TODO: seat guest to the seat here
                    if (seat.SitGuest(dragObject.elem.id) == false) {
                        self.onDragCancel(dragObject);
                    }
                    else {
                        dragObject.elem.parentNode.removeChild(dragObject.elem);

                        // seat guest on the seat
                        self.onDragEnd(dragObject, dropElem);
                    }
                }
            }
            else {
                self.onDragEnd(dragObject, dropElem);
            }
        }
        dragObject = {};
    }

    function createAvatar(e) {

        // запомнить старые свойства, чтобы вернуться к ним при отмене переноса
        var avatar = dragObject.elem;
        var old = {
            parent: avatar.parentNode,
            nextSibling: avatar.nextSibling,
            position: avatar.style.position || '',
            left: avatar.style.left || '',
            top: avatar.style.top || '',
            zIndex: avatar.style.zIndex || ''
        };

        // функция для отмены переноса
        avatar.rollback = function () {
            old.parent.insertBefore(avatar, old.nextSibling);
            avatar.style.position = old.position;
            avatar.style.left = old.left;
            avatar.style.top = old.top;
            avatar.style.zIndex = old.zIndex
        };

        return avatar;
    }

    function startDrag(e) {
        var avatar = dragObject.avatar;
        // hide all open menus
        HideAllSubMenu();

        // инициировать начало переноса
        document.body.appendChild(avatar);
        avatar.style.zIndex = 9999;
        avatar.style.position = 'absolute';
    }

    function findDraggable(event) {
        var elem = event.target;
        while (elem != document && elem.getAttribute('draggable') == null) {
            elem = elem.parentNode;
        }
        return elem == document ? null : elem;
    }

    function findDroppable(event) {

        var elem = getElementUnderClientXY(dragObject.avatar, event.clientX, event.clientY);

        while (elem != document && elem.getAttribute('droppable') == null) {
            elem = elem.parentNode;
        }

        return elem == document ? null : elem;
    }

    document.onmousemove = onMouseMove;
    document.onmouseup = onMouseUp;
    document.onmousedown = onMouseDown;

    this.onDragEnd = function (dragObject, dropElem) {
        // animation of End of drag and drop
        if (dragObject.elem.className == "resize_vertical") {
            dragObject.avatar.style.left = dragObject.downX - dragObject.shiftX + 'px'
        }
        if (dragObject.elem.className == "resize_horizontal") {
            dragObject.avatar.style.top = dragObject.downY - dragObject.shiftY + 'px';
        }

        if (dragObject.elem.className == "pot_guest") {
            if (dragAndDropSeatOver != null) {
                dragAndDropSeatOver.SelectSeat(false);
                dragAndDropSeatOver = null;

            }
        }

        kineticLayer.draw();
    };
    
    this.onDragCancel = function(dragObject) {
        // animation of cancel of drag and drop

        dragObject.avatar.rollback();
        VerticalResizePositionChanged();
        HorizontalResizePositionChanged();
    };
    
    this.onMoveAnimation = function (dragObject, fixedE) {
        // animation of moving of drag and drop

        var plannerField = document.getElementById("plannerCanvas");

        var lClassName = dragObject.elem.className;

        switch (lClassName) {
            case "resize_horizontal":
                if (fixedE.pageX > _minimumPlanWidth + parseInt(plannerField.style.left)) {
                    dragObject.avatar.style.left = fixedE.pageX - dragObject.shiftX + 'px';
                    dragObject.avatar.style.top = dragObject.downY - dragObject.shiftY + 'px';

                    // change planner size
                    HorizontalResizePositionChanged();
                }
                break;
            case "resize_vertical":
                if (fixedE.pageY > _minimumPlanHeight + parseInt(plannerField.style.top)) {
                    dragObject.avatar.style.left = dragObject.downX - dragObject.shiftX + 'px';
                    dragObject.avatar.style.top = fixedE.pageY - dragObject.shiftY + 'px';
                    // change planner size
                    VerticalResizePositionChanged();
                }
                break;
            case "pot_guest":
                dragObject.avatar.style.left = fixedE.pageX - dragObject.shiftX + 'px';
                dragObject.avatar.style.top = fixedE.pageY - dragObject.shiftY + 'px';

                var plannerField = document.getElementById("plannerCanvas");

                // drop on table plan. Check for seats.
                var seat = tablePlan.GetSeatUnderClientXY(fixedE.pageX - parseInt(plannerField.style.left), fixedE.pageY - parseInt(plannerField.style.top));
                if (seat != null) {
                    if (dragAndDropSeatOver != null)
                    {
                        if (dragAndDropSeatOver.SeatID != seat.SeatID) {
                            dragAndDropSeatOver.SelectSeat(false);
                            dragAndDropSeatOver = null;
                        }
                    }

                    dragAndDropSeatOver = seat;
                    seat.SelectSeat(true);
                    kineticLayer.draw();
                }
                else {
                    if (dragAndDropSeatOver != null) {
                        dragAndDropSeatOver.SelectSeat(false);
                        dragAndDropSeatOver = null;
                        kineticLayer.draw();
                    }
                }
                break;
            default:
                dragObject.avatar.style.left = fixedE.pageX - dragObject.shiftX + 'px';
                dragObject.avatar.style.top = fixedE.pageY - dragObject.shiftY + 'px';
                break;
        }
    }
};