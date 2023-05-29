<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  options: {
    type: Array,
  },
  modelValue: {
    type: String,
  },
});
const selectedOption = ref(props.modelValue);
let emit = defineEmits(["update:modelValue"]);
</script>
<template>
  <label
    class="container"
    v-for="(option, index) in options"
    :key="index"
    :class="[option.disabled == 'true' ? 'disabled_option' : null]"
    >{{ option.label }}
    <input
      type="radio"
      :value="selectedOption"
      name="radio"
      @change="emit('update:modelValue', option.value)"
      :checked="selectedOption !== '' ? true : false"
      :disabled="option.disabled == 'true' ? option.disabled : null"
    />
    <span class="checkmark"></span>
  </label>
</template>
<style lang="scss">
.disabled_option {
  opacity: 0.7;
  pointer-events: none;
}

/* The container */
.container {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 20px;
  cursor: pointer;
  font-weight: 400;
  font-size: 18px;
  line-height: 21px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 18px;
  width: 18px;
  background-color: #fff;
  border-radius: 50%;
  border: 1px solid #9b9b9b;
}

/* On mouse-over, add a grey background color */

.container:hover input ~ .checkmark {
  border: 1px solid #9b9b9b;
}

/* When the radio button is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #000;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.container .checkmark:after {
  border-radius: 50%;
  background: #000;
  border: 1px solid #000;
  top: 8px;
  left: 8px;
}
</style>
