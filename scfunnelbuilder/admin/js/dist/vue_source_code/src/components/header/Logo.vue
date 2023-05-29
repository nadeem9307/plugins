<script setup>
import brand_logo from "@/assets/img/brand_logo.svg"
import BaseInput from "../inputs/BaseInput.vue";
import { ref, onMounted, onUpdated } from 'vue';
import edit from "@/assets/icons/edit.svg";
import check_mark from "@/assets/icons/check_icon.svg";
import cancel from "@/assets/icons/cancel.svg";
import Icon from "../Icons/Icon.vue";
import { useNodeStore } from "@/store/nodes";
import { updateFunnelTitle } from "@/api/funnel/funnel-service";
import BaseButton from "../buttons/BaseButton.vue";
const nodeStore = useNodeStore()
let updateName = ref(nodeStore.getFunnelTitle);
let updateNameToggle = ref(false);
const inputRef = ref(null);
const nameEditToggle = () => {
    inputRef.value?.focus();
    updateNameToggle.value = !updateNameToggle.value;

}
onUpdated(async () => {
    inputRef.value?.focus()
})
const cancelHandler = async () => {
    updateNameToggle.value = false;
}

const changeNameHandler = async () => {
    if (!updateName.value) {

        updateName.value = nodeStore.getFunnelTitle;
        updateNameToggle.value = false;
        return;
    }
    const dataObj = {
        funnel_id: nodeStore.getFunnelId,
        funnel_name: updateName.value
    }
    await updateFunnelTitle(dataObj);
    updateNameToggle.value = false;
}


</script>

<template>
    <div class="brand_wrapper">
        <a class="brand_logo">
            <img :src="brand_logo" alt="logo">
        </a>
        <div v-if="updateNameToggle" class="change_titleName">
            <div class="update_titile">
                <div class="input_wrap">
                    <input ref="inputRef" v-model="updateName" class="form-control base_input" />
                </div>
                <BaseButton class="icons_button outline_btn" @on-click="changeNameHandler">
                    <Icon :name="check_mark" class="icons_size" />
                </BaseButton>
                <BaseButton class="icons_button outline_btn ms-2" @on-click="cancelHandler">
                    <Icon :name="cancel" class="icons_size" />
                </BaseButton>
            </div>
        </div>
        <div v-else class="d-flex align-items-center">
            <div class="brand_name">
                <h4 class="title">{{ updateName }}</h4>
            </div>
            <div class="edit_icon ms-2" @click="nameEditToggle">
                <Icon :name="edit" class="icon_size" />
            </div>
        </div>

    </div>
</template>

<style lang="scss">
.input_wrap {
    width: 60%;
}

.outline_btn {
    padding: 6px;
    cursor: pointer;

    &:hover {
        padding: 6px;
    }
}

.icons_size {
    widows: 24px;
    height: 24px;
    object-fit: contain;
}

.update_titile {
    display: flex;
    align-items: center;
    width: inherit;

    input {
        border: 0 !important;
        width: 100%;
        padding: 6px 10px;
    }
}

.brand_wrapper {
    display: flex;
    align-items: center;

    .brand_logo {
        width: 45px;
        height: 45px;
        line-height: 45px;
        display: block;
        margin-right: 16px;
        cursor: pointer;

        img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
    }

    .brand_name {}
}

.edit_icon {
    padding-right: 14px;
    cursor: pointer;
}

.icon_size {
    width: 24px;
    height: 24px;
}
</style>