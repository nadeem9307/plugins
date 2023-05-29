<script setup>
import eyeOutline from "@/assets/icons/eye_outline.svg";
import download from "@/assets/icons/download.svg";
import tempaleImage from "@/assets/img/theme_img.svg";
import Icon from "@/components/Icons/Icon.vue";
import { useNodeStore } from "@/store/nodes";
import { importTemplate } from "@/api/funnel/funnel-service"
import Loader from "@/components/loader/Loader.vue";
import { ref } from "vue";
import { getLastNode } from "@/utils/Common";
let loading = ref(false);


const props = defineProps({
    templateData: Object,
    required: true
})


const nodeStore = useNodeStore();
const templateHandler = async (tempData, formData, nodeId, productId = '',) => {
    loading.value = true;
    const templateData = {
        title: tempData?.page_title ? tempData?.page_title : '',
        download_url: tempData?.download_link ? tempData?.download_link : '',
        funnelID: nodeStore.getFunnelId,
        slug_type: nodeStore.getActiveStep?.slug_type,
        design_type: 'create_new',
        step_node_id: nodeId,
        step_product_id: formData?.product ? formData?.product : '',
        product_pricing_plan: formData?.pricePlan ? formData?.pricePlan : ''

    }
    const { error } = await importTemplate(templateData);
    loading.value = false;
    if (error) return;
    nodeStore.closeModal();
}
const emit = defineEmits(['save']);
defineExpose({ templateHandler })
</script>

<template>
    <div class="template_card">
        <div class="template_card_body">
            <img :src="templateData.featured_image" alt="template" class="temp_img" />
            <div class="import_temp" @click="$emit('save', $event)">
                <template v-if="!loading">
                    <Icon :name="download" alt="download" class="icon_a" />
                    <h5 class="ms-1">import</h5>
                </template>
                <Loader v-else />
            </div>
        </div>
        <div class="template_card_footer">
            <h4 class="sub_title">{{ templateData.category }}</h4>
            <a :href="templateData.preview_link" target="_blank">
                <Icon :name="eyeOutline" alt="eye_outline" class="icon_a" />
            </a>
        </div>
    </div>
</template>
<style lang="scss">
.template_card {
    background: #FFFFFF;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
}

.template_card_body {
    padding: 1px;
    padding-bottom: 0;
    position: relative;
    width: 100%;
    height: 200px;

    .temp_img {
        object-fit: cover;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
    }

    .import_temp {
        display: none;
        align-items: center;
        justify-content: center;
        transition: all 0.5s ease-in-out;
        cursor: pointer;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: #000000;
        opacity: 0.7;

        h5 {
            font-weight: 400;
            font-size: 18px;
            color: #fff;
            text-transform: capitalize;
        }

        .icon_a {
            width: 22px;
            height: 22px;
        }
    }

    &:hover {
        .import_temp {
            display: flex;
            z-index: 9;
        }
    }
}

.template_card_footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    background-color: #F5F5F5;

    .icon_a {
        width: 22px;
        height: 22px;
    }
}
</style>