<script setup>
import { ref } from 'vue';

const props = defineProps({
    minValue: {
        type: Number,
        default: 0,
    },
    maxValue: {
        type: Number,
        default: 100,
    },
    rangeId: {
        type: String,
    },
    modelValue: {
        type: String,
        default: 50,
    },
});

// const sliderValue = ref(props.value);
// const { emit } = defineEmits(['update:value']);
let emit = defineEmits(['update:modelValue']);
</script>
<template>
    <div class="slidecontainer">
        <input type="range" :min="minValue" class="range_slider" :name="rangeId" :id="rangeId" :max="maxValue"
            :value="modelValue" @input="emit('update:modelValue', $event.target.value)" />

    </div>
</template>
<style lang="scss">
.slidecontainer {
    width: 100%;
}

.range_slider {
    position: relative;
    -webkit-appearance: none;
    width: 100%;
    height: 3px;
    background: #B5BABF;
    outline: none;
    opacity: 0.7;
    -webkit-transition: .2s;
    transition: opacity .2s;
}

.range_slider .range_slider:hover {
    opacity: 1;
}

.range_slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #383838;
    cursor: pointer;

}


.range_slider::-moz-range-thumb {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #383838;
    cursor: pointer;
}
</style>