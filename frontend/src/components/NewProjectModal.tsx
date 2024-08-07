"use client";
import {Modal, Form, Input, Select, InputNumber} from 'antd';
import {useCallback, useMemo} from "react";
import useCategories from "@/api/useCategories";
import useNewProject from "@/api/useNewProject";

type NewProjectFormType = {
    name?: string;
    description?: string;
    meta: number;
    category: string;
};

type LoginModalProps = {
    open: boolean;
    setOpen: (open: boolean) => void;
};

export default function NewProjectModal({ open, setOpen }: LoginModalProps) {
    const [form] = Form.useForm<NewProjectFormType>();
    const {
        mutate: newProjectMutate,
        status
    } = useNewProject({
        successCallback: () => {
            form.resetFields()
            setOpen(false);
        }
    });

    const {
        data: categories,
        isLoading: categoriesLoading
    } = useCategories()

    const categoriesSelect = useMemo(() => {
        return categories?.map(category => ({
            label: category[0].toUpperCase() + category.slice(1),
            value: category
        }))
    }, [categories])

    const onFinish = useCallback(async (values: NewProjectFormType) => {
        newProjectMutate(values);
    }, [newProjectMutate])

    return <Modal
        open={open}
        title="New Project"
        okText="Create"
        cancelText="Cancel"
        okButtonProps={{
            loading: status === 'pending'
        }}
        cancelButtonProps={{
            disabled: status === 'pending'
        }}
        onCancel={() => {
            form.resetFields()
            setOpen(false);
        }}
        onOk={() => {
            form.submit();
        }}
    >
        <Form
            name={'login'}
            form={form}
            preserve={false}
            onFinish={onFinish}
            layout={"vertical"}
        >

            <Form.Item<NewProjectFormType>
                label="Name"
                name="name"
                rules={[
                    {
                        required: true,
                        message: 'Name is required'
                    }
                ]}
                >
                <Input />
            </Form.Item>

            <Form.Item<NewProjectFormType>
                label="Description"
                name="description"
                rules={[
                    {
                        required: true,
                        message: 'Description is required'
                    }
                ]}
                >
                <Input.TextArea />
            </Form.Item>

            <Form.Item<NewProjectFormType>
                label="Meta"
                name="meta"
                rules={[
                    {
                        required: true,
                        message: 'Meta is required'
                    }
                ]}
                >
                <InputNumber<number>
                    style={{ width: '100%' }}
                    placeholder="Meta"
                    formatter={(value) => `$ ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')}
                    parser={(value) => value?.replace(/\$\s?|(,*)/g, '') as unknown as number}
                />
            </Form.Item>

            <Form.Item<NewProjectFormType>
                label="Category"
                name="category"
                rules={[
                    {
                        required: true,
                        message: 'Category is required'
                    }
                ]}
                >
                <Select
                    loading={categoriesLoading}
                    placeholder="Category"
                    options={categoriesSelect}
                />
            </Form.Item>
        </Form>
    </Modal>
}