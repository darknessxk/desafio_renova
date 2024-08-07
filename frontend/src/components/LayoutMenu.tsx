"use client";
import {Menu} from "antd";
import {MenuItemType} from "antd/lib/menu/interface";
import {usePathname, useRouter} from "next/navigation";
import {useUser} from "@/helper/user";
import {useEffect, useState} from "react";
import LoginModal from "@/components/LoginModal";
import RegisterModal from "@/components/RegisterModal";

export default function LayoutMenu() {
    const path = usePathname();
    const user = useUser();
    const [items, setItems] = useState<MenuItemType[]>([]);
    const [loginModalOpen, setLoginModalOpen] = useState(false);
    const [registerModalOpen, setRegisterModalOpen] = useState(false);
    const router = useRouter()

    useEffect(() => {
        const base = [
            {
                title: 'Home',
                key: '/',
                label: 'Home',
            }
        ]

        if (user.isAuthenticated) {
            base.push({
                title: 'My Projects',
                key: '/my-projects',
                label: 'My Projects',
            })

            base.push({
                title: 'Logout',
                key: '/logout',
                label: 'Logout',
            })
        } else {
            base.push({
                title: 'Login',
                key: '/login',
                label: 'Login',
            })

            base.push({
                title: 'Register',
                key: '/register',
                label: 'Register',
            })
        }

        setItems(base);
    }, [user.isAuthenticated]);

    return (
        <>
            <Menu
                theme="dark"
                mode="horizontal"
                items={items}
                selectedKeys={[path]}
                onClick={({ key }) => {
                    switch (key) {
                        case '/':
                            router.push('/');
                            break;
                        case '/logout':
                            user.clearToken();
                            break;
                        case '/login':
                            setLoginModalOpen(true);
                            break;
                        case '/register':
                            setRegisterModalOpen(true);
                            break;
                        case '/my-projects':
                            router.push('/my-projects');
                            break;
                        default:
                            console.log('Navigate to', key);
                    }
                }}
                style={{ flex: 1, minWidth: 0, justifyContent: "flex-end" }}
            />
            <LoginModal open={loginModalOpen} setOpen={setLoginModalOpen} />
            <RegisterModal open={registerModalOpen} setOpen={setRegisterModalOpen} />
        </>
    )
}