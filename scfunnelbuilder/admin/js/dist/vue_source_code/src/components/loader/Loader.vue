<template>
    <div class="loader_wrapper">
        <div class="custom-loader loader_custom"></div>
    </div>
</template>
<style lang="scss">
.custom-loader {
    width: 12px;
    height: 12px;
    background: #000;
    border-radius: 50%;
    box-shadow: 20px 0 #939393, -20px 0 #000;
    animation: d5 1s infinite linear alternate;
}

@keyframes d5 {
    0% {
        box-shadow: 20px 0 #000, -20px 0 #939393;
        background: #000
    }

    33% {
        box-shadow: 20px 0 #000, -20px 0 #939393;
        background: #939393
    }

    66% {
        box-shadow: 20px 0 #939393, -20px 0 #000;
        background: #939393
    }
}
</style>