<script setup>
import { ref, onMounted } from 'vue';
import { useField } from 'vee-validate';
import * as yup from 'yup';
const { value, errorMessage } = useField(props.name,
    yup.string()
        .required(`${props.name} is required`)
    // .matches(/^(?!.*\^)[a-zA-Z]+$/, "Only alphabets allowed"),
);
const inputEle = ref(null);
const props = defineProps({
    type: {
        type: String,
        default: 'text'
    },
    isReadable: {
        type: Boolean,
        default: false
    },
    name: {
        type: String,
    },
    labelName: {
        type: String,
    },
    modelValue: String | undefined,
    // modelValue: {
    //     type: String,
    //     required: true
    // }
})
// onMounted(() => {
//     const element = inputEle.value;
//     element.focus();
// })
let emit = defineEmits(['update:modelValue']);
defineExpose({ inputEle })
</script>
<template>
    <label v-if="labelName" class="base_inp_label">{{ labelName }}</label>
    <input ref="inputEle" :value="value" @input="emit('update:modelValue', $event.target.value)" :type="type" :name="name"
        class="form-control base_input" :class="[errorMessage ? 'has_error' : '']" :readonly="isReadable">
    <span class="error_message">{{ errorMessage }}</span>
</template>
<style lang="scss"></style>