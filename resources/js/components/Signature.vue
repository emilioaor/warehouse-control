<template>
    <section v-show="isOpen">

        <div>
            <canvas
                    id="canvas"
                    style="border: 1px solid black;"
                    @touchmove="move"
                    @touchstart="start"
                    @touchend="drawing = false"
                    @mousedown="start"
                    @mousemove="move"
                    @mouseup="drawing = false"
                    :width="width"
                    :height="height"
            ></canvas>

            <div>
                <button type="button" class="btn btn-primary" @click="save()">
                    {{ t('form.save') }}
                </button>

                <button type="button" class="btn btn-warning" @click="clear()">
                    {{ t('form.clear') }}
                </button>

                <button type="button" class="btn btn-danger" @click="close()">
                    {{ t('form.close') }}
                </button>
            </div>
        </div>

    </section>
</template>

<script>
    export default {
        data() {
            return {
                currX: null,
                currY: null,
                prevX: null,
                prevY: null,
                drawing: false,
                canvas: null,
                ctx: null,
                color: "black",
                ready: false,
                width: 0,
                height: 0,
                isOpen: false
            }
        },
        mounted() {
            this.canvas = document.getElementById('canvas');
            this.ctx = canvas.getContext('2d');
            this.ctx.strokeStyle = this.color;
            this.width = window.screen.width * 0.8;

            if (this.width < 300) {
                this.width = 300;
            }

            if (this.width > 700) {
                this.width = 700;
            }

            this.height = this.width * 0.35;
        },
        methods: {
            start(event) {
                this.drawing = true;
                this.currX = event.offsetX !== undefined ? event.offsetX : event.touches[0].pageX - event.touches[0].target.offsetLeft;
                this.currY = event.offsetY !== undefined ? event.offsetY : event.touches[0].pageY - event.touches[0].target.offsetTop;
                this.ctx.moveTo(this.currX, this.currY);
            },
            move: function(event) {
                this.prevX = this.currX;
                this.prevY = this.currY;

                this.currX = event.offsetX !== undefined ? event.offsetX : event.touches[0].pageX - event.touches[0].target.offsetLeft;
                this.currY = event.offsetY !== undefined ? event.offsetY : event.touches[0].pageY - event.touches[0].target.offsetTop;

                if (this.drawing) {
                    this.ctx.lineTo(this.currX, this.currY);
                    this.ctx.stroke();
                    this.ready = true;
                }
            },
            clear() {
                this.currX = null;
                this.currY = null;
                this.prevX = null;
                this.prevY = null;
                this.drawing = false;
                this.ready = false;
                this.canvas.width = this.canvas.width;
            },
            save() {
                this.isOpen = false;
                this.$emit('save', {base64: this.canvas.toDataURL()});
                this.clear();
            },
            close() {
                this.isOpen = false;
                this.$emit('close');
                this.clear();
            },
            open() {
                this.isOpen = true;
            }
        }
    }
</script>

<style scoped lang="scss">
    section {
        background-color: rgba(0,0,0,.3);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;

        > div {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 5px;
        }
    }
</style>