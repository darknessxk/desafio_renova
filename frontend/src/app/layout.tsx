import type { Metadata } from "next";
import { AntdRegistry } from '@ant-design/nextjs-registry';
import Providers from "@/app/providers";
import Layout, {Content, Footer, Header} from "antd/lib/layout/layout";
import "./global.css"
import LayoutMenu from "@/components/LayoutMenu";

export const metadata: Metadata = {
    title: "Frontend Side",
};

export default function RootLayout({ children, }: Readonly<{
    children: React.ReactNode;
}>) {
    return (
        <html lang="en">
        <body style={{ margin: 0 }}>
        <AntdRegistry>
            <Providers>
                <Layout style={{ minHeight: "100vh" }}>
                    <Header className={"flex items-center justify-between"}>
                        <h3 className={`text-white md:text-2xl sm:text-4xl font-bold m-0`}>Frontend Challenge</h3>
                        <LayoutMenu />
                    </Header>
                    <Content className={"p-8"}>{children}</Content>
                    <Footer>
                        <span>Luiz Felipe @ 2024</span>
                    </Footer>
                </Layout>
            </Providers>
        </AntdRegistry>
        </body>
        </html>
    );
}
