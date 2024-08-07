import {Modal, Form} from 'antd';
import {KeyOutlined, UserOutlined} from '@ant-design/icons';
import { Input } from 'antd';
import useLogin from "@/api/useLogin";
import {useCallback} from "react";

type LoginFormType = {
    email?: string;
    password?: string;
};

type LoginModalProps = {
    open: boolean;
    setOpen: (open: boolean) => void;
};

export default function LoginModal({ open, setOpen }: LoginModalProps) {
    const [form] = Form.useForm<LoginFormType>();
    const { mutate: loginMutate, status } = useLogin({
        successCallback: () => {
            form.resetFields();
            setOpen(false);
        }
    });

    const onFinish = useCallback(async (values: LoginFormType) => {
        if (!values.email || !values.password) {
            return;
        }

        loginMutate({
            username: values.email,
            password: values.password
        });
    }, [loginMutate])

    return <Modal
        open={open}
        title="Login"
        okText="Login"
        okButtonProps={{ loading: status === 'pending' }}
        cancelButtonProps={{ disabled: status === 'pending' }}
        cancelText="Cancel"
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
        >
            <Form.Item<LoginFormType>
                name="email"
                rules={[
                    { required: true, message: 'Please input your email!' },
                    { type: "email" }
                ]}
            >
                <Input placeholder="Email" prefix={<UserOutlined />} type="email" />
            </Form.Item>

            <Form.Item<LoginFormType>
                name="password"
                rules={[{ required: true, message: 'Please input your password!' }]}
            >
                <Input.Password placeholder="Password" prefix={<KeyOutlined />} />
            </Form.Item>
        </Form>
    </Modal>
}