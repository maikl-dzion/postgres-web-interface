const dragMixin = {

    data: function () {
        return {
            dragStart : false,
            _left  : 4,
            _top   : 4,
            dragParam : {
                x : 0,
                y : 0,
                target  : {},
                current : {},
                elem    : {},
                id      : 0,
            },
            // oldMouseX : 0,
            // oldMouseY : 0,
            // dragElem : {},
            // elemId   : '',
        }
    },  // Data

    methods: {

        dragInit(e) {
            this.dragStart = true;
            this.dragParam.x = e.clientX;
            this.dragParam.y = e.clientY;
            this.dragParam.target = e.target;
            this.dragParam.elem   = e.currentTarget;
            this.dragParam.id     = e.currentTarget.id;
        },

        dragMove(e) {
            if(!this.dragStart) return;
            if(this.dragParam.x == e.clientX &&
               this.dragParam.y == e.clientY) return;

            this.moveElem(e);
        },

        dragStop(e) {
            this.dragStart = false;
        },

        moveElem(e) {

            var leftMove = 0;
            var topMove  = 0;
            var newLeft = newTop = '';

            let id = this.dragParam.id;
            let x  = this.dragParam.x;
            let y  = this.dragParam.y;
            let newX = e.clientX;
            let newY = e.clientY;

            var pos = this.getPosition('#' + id);
            var elem = pos.elem;

            // Движение по горизонтали
            this.updateElem(x, newX, pos, 'left', elem);

            // Движение по вертикали
            this.updateElem(y, newY, pos, 'top', elem);

            this.dragParam.x = newX;
            this.dragParam.y = newY;
        },

        getPosition(selector) {
            var elem = document.querySelector(selector);
            var left  = elem.style.left;
            var top   = elem.style.top;
            left = parseInt(left.replace(/\D+/g,""));
            top = parseInt(top.replace(/\D+/g,""));
            return { left, top, elem };
        },

        updateElem(p1, p2, pos, attr, elem) {
            var offset = newAtrr = '';
            if(p1 < p2) {
                offset = p2 - p1;
                newAtrr =  pos[attr] + offset;
            } else {
                offset = p1 - p2;
                newAtrr =  pos[attr] - offset;
            }
            elem.style[attr] = newAtrr + 'px';
        },


    }  // Methods
};


// moveElem(e) {
//
//     let id = this.dragParam.id;
//     let x  = this.dragParam.x;
//     let y  = this.dragParam.y;
//     let newX = e.clientX;
//     let newY = e.clientY;
//     var elem = document.querySelector('#' + id);
//     var oldLeft  = elem.style.left;
//     var oldTop   = elem.style.top;
//
//
//     var leftMove = 0;
//     var topMove  = 0;
//     oldLeft = parseInt(oldLeft.replace(/\D+/g,""));
//     oldTop = parseInt(oldTop.replace(/\D+/g,""));
//     var newLeft = newTop = '';
//
//     // Движение по горизонтали
//     if(x < newX) { // сдвинулись вправо
//         leftMove = newX - x;
//         newLeft =  oldLeft + leftMove;
//         elem.style.left = newLeft + 'px';
//     } else {  // сдвинулись влево
//         leftMove = x - newX;
//         newLeft =  oldLeft - leftMove;
//         elem.style.left = newLeft + 'px';
//     }
//
//     // Движение по вертикали
//     if(y < newY) { // сдвинулись вниз
//         topMove = newY - y;
//         newTop =  oldTop + topMove;
//         elem.style.top = newTop + 'px';
//     } else {  // сдвинулись вверх
//         topMove = y - newY;
//         newTop =  oldTop - topMove;
//         elem.style.top = newTop + 'px';
//     }
//
//     this.dragParam.x = newX;
//     this.dragParam.y = newY;
// },