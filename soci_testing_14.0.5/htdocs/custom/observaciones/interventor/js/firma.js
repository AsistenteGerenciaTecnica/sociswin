    const canvas = document.getElementById("canvas");
    const clearBtn = document.getElementById("clear");
    const ctx = canvas.getContext("2d");

    let canvasOffsetX = canvas.offsetLeft;
    let canvasOffsetY = canvas.offsetTop;

    console.log(canvas.parentNode.getAttribute("style", "height"));

    canvas.width = canvas.parentNode.offsetWidth;
    canvas.height = canvas.parentNode.offsetHeight;

    let isPainting = false;
    let lineWidth = 5;

    let startX;
    let startY;

    function drawMouse(e) {
        if (!isPainting) {
            return;
        }

        canvasOffsetX = canvas.offsetLeft;
        canvasOffsetY = canvas.offsetTop;

        ctx.lineWidth = lineWidth;
        ctx.lineCap = "round";

        ctx.lineTo(e.clientX - canvasOffsetX, e.clientY - canvasOffsetY);
        ctx.stroke();
    }
    
    function drawTouch(e) {
        e.preventDefault();

        if (!isPainting) {
            return;
        }

        canvasOffsetX = canvas.offsetLeft;
        canvasOffsetY = canvas.offsetTop;

        ctx.lineWidth = lineWidth;
        ctx.lineCap = "round";

        let touches = e.changedTouches;
        console.log(touches);

        for (let i = 0; i < touches.length; i++) {

            var idx = touches[i].identifier;
            
            ctx.lineTo(touches[i].clientX - canvasOffsetX, touches[i].clientY - canvasOffsetY);
            ctx.stroke();
        }
    }

    function onTouch(e) {
        isPainting = true;
    }

    function onRelease() {
        isPainting = false;
        ctx.stroke();
        ctx.beginPath();
    }

    clearBtn.addEventListener("click", (e) => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });

    canvas.addEventListener("mousedown", (e) => {
        onTouch(e);
    });
    canvas.addEventListener("touchstart", (e) => {
        onTouch(e);
    });

    canvas.addEventListener("mouseup", onRelease);
    canvas.addEventListener("mouseleave", onRelease)
    canvas.addEventListener("touchend", onRelease);

    canvas.addEventListener("mousemove", (e) => {
        drawMouse(e);
    });
    canvas.addEventListener("touchmove", (e) => {
        drawTouch(e);
    });


    let saveBtn = document.getElementById("save");

    saveBtn.addEventListener("click", (e) => {
        let canvas_content = document.getElementById("canvas_content");
        
        canvas_content.value = canvas.toDataURL("image/png");

        document.forms["canvas_form"].submit();
    })