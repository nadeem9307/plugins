<script setup>
const emit = defineEmits(['onChange'])
const props = defineProps({
    isChecked: {
        type: Boolean,
        default: false
    },
    title: String

})
</script>
<template>
    <div class="base_switch">
        <span class="switch_name">{{ title }}</span>
        <label class="switch">
            <input type="checkbox" @change="$emit('onChange', $event)" :checked="isChecked">
            <span class="slider round"></span>
        </label>
    </div>
</template>

<style>
.base_switch {
    display: flex;
    align-items: center;
}

.switch_name {
    font-weight: 500;
    font-size: 15px;
    line-height: 18px;
    color: #000000;
    margin-right: 8px;
}

.switch {
    position: relative;
    display: inline-block;
    width: 54px;
    height: 28px;
    margin-bottom: 0;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;

}

.switch input:focus {
    border: 0;
    outline: none;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 21px;
    width: 21px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
}

input:checked+.slider {
    background-color: #c1fa46;

}

input:focus+.slider {
    box-shadow: none;
}

input:checked+.slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}
</style>