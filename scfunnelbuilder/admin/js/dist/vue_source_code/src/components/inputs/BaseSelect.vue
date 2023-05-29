<script setup>
import Multiselect from '@vueform/multiselect'
import { ref } from 'vue';
import { useField } from 'vee-validate';
import * as yup from 'yup';
const { value, errorMessage } = useField(props.name,
    yup.string()
        .required(`${props.name} is required`)
);
let isLoaderVisible = ref(false);

const props = defineProps({
    label: String,
    placeholder: {
        type: String,
    },
    selectedOption: {
        type: Number,
    },
    name: {
        type: String,
    },
    optionsData: {
        type: Array,
    }
})
const emit = defineEmits(['onSelect', 'unselect']);


const openHandler = () => {
    isLoaderVisible.value = true;
}
const closeHandler = () => {
    isLoaderVisible.value = false;
}
</script>
<template>
    <div v-if="label" class="select_box_wrapper">
        <h4 class="sub_title pe-3">{{ label }}</h4>
        <div class="single_select">
            <Multiselect v-model="value" @deselect="$emit('unselect', $event)" :canDeselect="false" @open="openHandler"
                @close="closeHandler" :loading="isLoaderVisible && !optionsData?.length" @select="$emit('onSelect', $event)"
                :placeholder="placeholder" :options="optionsData" class="multi_select_input"
                :class="[errorMessage ? 'has_error' : '']" />
            <span class="error_message">{{ errorMessage }}</span>
        </div>
    </div>
    <div v-else class="select_box_wrapper">
        <Multiselect v-model="value" @select="$emit('onSelect', $event)" :canDeselect="false" @open="openHandler"
            @close="closeHandler" :loading="isLoaderVisible && !optionsData?.length" :placeholder="placeholder"
            :options="optionsData" class="multi_select_input" :class="[errorMessage ? 'has_error' : '']" />
        <span class="error_message">{{ errorMessage }}</span>

    </div>
</template>