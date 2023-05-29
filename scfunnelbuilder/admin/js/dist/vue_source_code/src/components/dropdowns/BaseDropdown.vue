<script setup>

</script>
<template>
    <div class="drop_down_container">
        <ul>
            <li>Preview</li>
            <li>Clone</li>
            <li>Delete</li>
        </ul>
    </div>
</template>
<style lang="scss">
.drop_down_container {
    background: #FFFFFF;
    box-shadow: 0px 4px 2px rgba(0, 0, 0, 0.05);
    border-radius: 1px;

    ul {
        padding-left: 0;
        margin-bottom: 0;

        li {
            padding: 10px 16px;
            font-weight: 400;
            font-size: 17px;
            line-height: 20px;
            color: #000000;
            background-color: transparent;
            border-radius: 3px;
            list-style: none;

            &:last-child {
                margin-bottom: 0;
            }

            &:hover {
                cursor: pointer;
                background-color: #cdcdcd;
            }

        }
    }
}
</style>