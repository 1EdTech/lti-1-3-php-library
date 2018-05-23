var c = document.getElementById("breakout");
var ctx = window.c.getContext("2d");

var score = 0;

var ball = {
    pos : {
        x: window.c.width/2-200,
        y : window.c.height/2-2,
    },
    vel : {
        x : 6,
        y : 6,
    },
    r: 10,
    render: function() {
        this.pos.x += this.vel.x;
        this.pos.y += this.vel.y;
        if (this.oob(window.c.width, this.pos.x, this.r)) {
            this.vel.x = -this.vel.x;
            this.pos.x += this.vel.x;
            this.pos.y -= this.vel.y;
        }
        if (this.oob(window.c.height, this.pos.y, this.r)) {
            if (this.pos.y > window.c.height - this.r) {
                var xhttp = new XMLHttpRequest();
                xhttp.open("GET", "score.php?client_id=" + window.client_id + "&auth_url=" + encodeURIComponent(window.auth_url) + "&grade=" + window.score, true);
                xhttp.send();
                window.pause = true;
            }
            this.vel.y = -this.vel.y;
            this.pos.y += this.vel.y;
            this.pos.x -= this.vel.x;
        }

        window.ctx.beginPath();
        window.ctx.arc(this.pos.x, this.pos.y, this.r, 0, 2 * Math.PI);
        window.ctx.stroke();
    },
    oob : function(max, curr, offset) {
        if (curr < offset || curr > (max-offset)) {
            return true;
        }
    },
    left : function() {
        return this.pos.x - this.r;
    },
    right : function() {
        return this.pos.x + this.r;
    },
    top : function() {
        return this.pos.y - this.r;
    },
    bottom : function() {
        return this.pos.y + this.r;
    },
};

var paddle = {
    pos : {
        x: window.c.width/2+2,
        y : window.c.height-40,
    },
    width : 80,
    height : 20,
    render: function() {
        window.ctx.beginPath();
        window.ctx.rect(this.pos.x, this.pos.y, this.width, this.height);
        window.ctx.stroke();
    },
    left : function() {
        return this.pos.x;
    },
    right : function() {
        return this.pos.x + this.width;
    },
    top : function() {
        return this.pos.y;
    },
    bottom : function() {
        return this.pos.y + this.height;
    },
    test_hit : function() {
        var hitx = this.test_hit_x();
        var hity = this.test_hit_y();
        if (!hitx || !hity) {
            return 0;
        }
        if (hity) {
            window.ball.vel.y = -window.ball.vel.y;
            window.ball.pos.y += window.ball.vel.y;
            window.ball.pos.x -= window.ball.vel.x;
        }
        if (hitx) {
            var xdiff = window.ball.pos.x - (this.pos.x + (this.width/2));
            window.ball.vel.x = Math.ceil(xdiff / 5);
            window.ball.pos.x += window.ball.vel.x;
        }
        this.hit = 1;
        window.score++;
        return 1;
    },
    test_hit_x : function() {
        if (this.left() > window.ball.right()) {
            return 0;
        }
        if (this.right() < window.ball.left()) {
            return 0;
        }
        return 1;
    },
    test_hit_y : function() {
        if (this.top() > window.ball.bottom()) {
            return 0;
        }
        if (this.bottom() < window.ball.top()) {
            return 0;
        }
        return 1;
    },
    move : function() {
        if (window.press_left) {
            if (this.pos.x > 0) {
                this.pos.x -= 8;
            }
        }
        if (window.press_right) {
            if (this.pos.x < window.c.width - this.width) {
                this.pos.x += 8;
            }
        }
    }
};

function brick() {
    this.pos = {
        x: 40,
        y: 40,
    },
    this.hit = 0,
    this.last_hitx = false,
    this.last_hity = false,
    this.width = 40,
    this.height = 20,
    this.render = function() {
        if (this.hit) {
            return;
        }
        window.ctx.beginPath();
        window.ctx.rect(this.pos.x, this.pos.y, this.width, this.height);
        window.ctx.stroke();
    },
    this.test_hit = function() {
        if (this.hit) {
            return 0;
        }
        var hitx = this.test_hit_x();
        var hity = this.test_hit_y();
        if (!hitx || !hity) {
            this.last_hitx = hitx;
            this.last_hity = hity;
            return 0;
        }
        if (this.last_hity) {
            window.ball.vel.y = -window.ball.vel.y;
            window.ball.pos.y += window.ball.vel.y;
            window.ball.pos.x -= window.ball.vel.x;
        }
        if (this.last_hitx) {
            window.ball.vel.x = -window.ball.vel.x;
            window.ball.pos.x += window.ball.vel.x;
            window.ball.pos.y -= window.ball.vel.y;
        }
        if (!this.last_hity && this.last_hitx) {
            window.ball.vel.x = -window.ball.vel.x;
            window.ball.pos.x += window.ball.vel.x;
            window.ball.vel.y = -window.ball.vel.y;
            window.ball.pos.y += window.ball.vel.y;
        }
        this.last_hitx = hitx;
        this.last_hity = hity;
        this.hit = 1;
        window.score++;
        return 1;
    },
    this.test_hit_x = function() {
        if (this.left() > window.ball.right()) {
            return 0;
        }
        if (this.right() < window.ball.left()) {
            return 0;
        }
        return 1;
    },
    this.test_hit_y = function() {
        if (this.top() > window.ball.bottom()) {
            return 0;
        }
        if (this.bottom() < window.ball.top()) {
            return 0;
        }
        return 1;
    },


    this.left = function() {
        return this.pos.x;
    },
    this.right = function() {
        return this.pos.x + this.width;
    },
    this.top = function() {
        return this.pos.y;
    },
    this.bottom = function() {
        return this.pos.y + this.height;
    }
};

press_left = false;
press_right = false;

document.addEventListener('keydown', (event) => {
    const keyName = event.key;
    if (keyName == "ArrowLeft") {
        press_left = true;
    }
    if (keyName == "ArrowRight") {
        press_right = true;
    }
  });

document.addEventListener('keyup', (event) => {
    const keyName = event.key;
    if (keyName == "ArrowLeft") {
        press_left = false;
    }
    if (keyName == "ArrowRight") {
        press_right = false;
    }
});
var bricks = [];

for (var h = 0; h < 6; h++) {
    for (var w = 0; w < 18; w++) {
        var brickid = (18*h)+w;
        bricks[brickid] = new brick();
        bricks[brickid].pos.x = 40+(w*40);
        bricks[brickid].pos.y = 40+(h*20);
    }
}

pause = false;

var frame = function() {
    window.ctx.clearRect(0, 0, window.c.width, window.c.height);
    for (var i = 0; i < window.bricks.length; i++) {
        window.bricks[i].render();
        if (window.bricks[i].test_hit()) {
            break;
        }
    }
    window.paddle.move();
    window.paddle.render();
    window.paddle.test_hit();
    window.ball.render();
    if (!pause) {
        requestAnimationFrame(frame);
    }
    pause = false;
}
frame();
